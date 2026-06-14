<?php

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('administrator can create a product with uploaded gallery images and safe rich text', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Fertilizers', 'slug' => 'fertilizers']);

    $response = $this->actingAs($admin)->post(route('admin.products.store'), [
        'category_id' => $category->id,
        'name' => 'Uploaded Product',
        'price' => 2500,
        'stock_quantity' => 10,
        'manage_stock' => 1,
        'is_featured' => 1,
        'is_active' => 1,
        'description' => '<h2>Benefits</h2><p onclick="bad()">Safe text</p><script>alert(1)</script>',
        'uploads' => [
            ['file' => UploadedFile::fake()->image('front.webp'), 'alt_text' => 'Front pack', 'sort_order' => 0],
            ['file' => UploadedFile::fake()->image('back.jpg'), 'alt_text' => 'Back pack', 'sort_order' => 1],
        ],
        'primary_image' => 'new:1',
    ]);

    $product = Product::where('name', 'Uploaded Product')->firstOrFail()->load('images');
    $response->assertRedirect(route('admin.products.edit', $product));
    expect($product->images)->toHaveCount(2)
        ->and($product->images->firstWhere('is_primary', true)?->alt_text)->toBe('Back pack')
        ->and($product->description)->toContain('<h2>Benefits</h2>')
        ->and($product->description)->not->toContain('onclick')
        ->and($product->description)->not->toContain('<script');

    foreach ($product->images as $image) {
        Storage::disk('public')->assertExists($image->path);
    }

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('media/uploads/products/'.$product->id, false)
        ->assertSee('<h2>Benefits</h2>', false);

    $this->get($product->images->first()->url)
        ->assertOk()
        ->assertHeader('cache-control', 'immutable, max-age=31536000, public')
        ->assertHeader('x-content-type-options', 'nosniff');
});

test('administrator can upload and replace a blog featured image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $category = BlogCategory::create([
        'name' => 'Crop Guides',
        'slug' => 'crop-guides',
        'language' => 'en',
    ]);

    $this->actingAs($admin)->post(route('admin.blogs.store'), [
        'blog_category_id' => $category->id,
        'title' => 'Image Upload Guide',
        'language' => 'en',
        'content' => '<p>Useful <strong>guide</strong>.</p>',
        'status' => 'published',
        'published_at' => now(),
        'featured_image_file' => UploadedFile::fake()->image('cover.jpg'),
        'featured_image_alt' => 'Healthy wheat crop',
    ])->assertSessionHasNoErrors();

    $blog = Blog::where('title', 'Image Upload Guide')->firstOrFail();
    $oldPath = $blog->featured_image;
    Storage::disk('public')->assertExists($oldPath);

    $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'blog_category_id' => $category->id,
        'title' => $blog->title,
        'slug' => $blog->slug,
        'language' => 'en',
        'content' => '<p>Updated guide.</p>',
        'status' => 'published',
        'published_at' => now(),
        'featured_image_file' => UploadedFile::fake()->image('replacement.webp'),
        'featured_image_alt' => 'Updated wheat crop',
    ])->assertSessionHasNoErrors();

    $blog->refresh();
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($blog->featured_image);

    expect($blog->featured_image_url)
        ->toContain('/media/'.$blog->featured_image)
        ->and($blog->featured_image_alt)->toBe('Updated wheat crop');
});

test('public media route rejects paths outside uploads', function () {
    $this->get('/media/../.env')->assertNotFound();
    $this->get('/media/product-media/demo/agriculture-field-1.jpg')->assertNotFound();
});

test('admin published article without a date is immediately visible on frontend', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = BlogCategory::create([
        'name' => 'Immediate Guides',
        'slug' => 'immediate-guides',
        'language' => 'en',
    ]);

    $this->actingAs($admin)->post(route('admin.blogs.store'), [
        'blog_category_id' => $category->id,
        'title' => 'Immediately Published Article',
        'language' => 'en',
        'content' => '<p>This article should be public now.</p>',
        'status' => 'published',
        'published_at' => null,
    ])->assertSessionHasNoErrors();

    $blog = Blog::where('title', 'Immediately Published Article')->firstOrFail();

    expect($blog->published_at)->toBeNull()
        ->and($blog->isPubliclyVisible())->toBeTrue()
        ->and(Blog::published()->whereKey($blog)->exists())->toBeTrue();

    $this->get(route('blogs.english.index'))
        ->assertOk()
        ->assertSee($blog->title);

    expect(
        Blog::published()
            ->where('language', 'en')
            ->where('slug', $blog->slug)
            ->exists()
    )->toBeTrue();
});

test('future dated article remains scheduled until its publication time', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = BlogCategory::create([
        'name' => 'Scheduled Guides',
        'slug' => 'scheduled-guides',
        'language' => 'en',
    ]);

    $this->actingAs($admin)->post(route('admin.blogs.store'), [
        'blog_category_id' => $category->id,
        'title' => 'Scheduled Article',
        'language' => 'en',
        'content' => '<p>Not public yet.</p>',
        'status' => 'published',
        'published_at' => now()->addDay()->format('Y-m-d\TH:i'),
    ])->assertSessionHasNoErrors();

    $blog = Blog::where('title', 'Scheduled Article')->firstOrFail();

    expect(Blog::published()->whereKey($blog)->exists())->toBeFalse();
    $this->get(route('blogs.english.show', $blog->slug))->assertNotFound();
});

test('editing an article title does not silently change its published URL', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = BlogCategory::create([
        'name' => 'Stable URLs',
        'slug' => 'stable-urls',
        'language' => 'en',
    ]);
    $blog = Blog::create([
        'blog_category_id' => $category->id,
        'author_id' => $admin->id,
        'title' => 'Original Article Title',
        'language' => 'en',
        'content' => '<p>Original content.</p>',
        'status' => 'published',
    ]);
    $originalSlug = $blog->slug;

    $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'blog_category_id' => $category->id,
        'title' => 'Updated Article Title',
        'slug' => $originalSlug,
        'language' => 'en',
        'content' => '<p>Updated content.</p>',
        'status' => 'published',
    ])->assertSessionHasNoErrors();

    expect($blog->refresh()->slug)->toBe($originalSlug);
    expect(
        Blog::published()
            ->where('language', 'en')
            ->where('slug', $originalSlug)
            ->value('title')
    )->toBe('Updated Article Title');
});

test('article language must match its selected category language', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $urduCategory = BlogCategory::create([
        'name' => 'Urdu Category',
        'slug' => 'urdu-category',
        'language' => 'ur',
    ]);

    $this->actingAs($admin)->post(route('admin.blogs.store'), [
        'blog_category_id' => $urduCategory->id,
        'title' => 'English Article',
        'language' => 'en',
        'content' => '<p>English content.</p>',
        'status' => 'published',
    ])->assertSessionHasErrors('blog_category_id');

    expect(Blog::where('title', 'English Article')->exists())->toBeFalse();
});
