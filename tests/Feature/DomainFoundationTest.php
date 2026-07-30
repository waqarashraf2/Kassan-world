<?php

use App\Models\Blog;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\MagazinePurchase;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Video;
use App\Models\WebsiteSetting;
use App\Services\MagazineAccessService;
use App\Services\OrderService;
use Database\Seeders\KisanWorldContentSeeder;

test('products receive unique SEO slugs', function () {
    $category = Category::create(['name' => 'Fertilizers']);

    $first = Product::create([
        'category_id' => $category->id,
        'name' => 'Wheat Booster',
        'price' => 1000,
        'stock_quantity' => 10,
    ]);
    $second = Product::create([
        'category_id' => $category->id,
        'name' => 'Wheat Booster',
        'price' => 1200,
        'stock_quantity' => 10,
    ]);

    expect($first->slug)->toBe('wheat-booster')
        ->and($second->slug)->toBe('wheat-booster-2');
});

test('checkout creates an order and decrements managed stock', function () {
    $category = Category::create(['name' => 'Seeds']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Certified Wheat Seed',
        'price' => 2500,
        'discount_price' => 2200,
        'stock_quantity' => 5,
    ]);

    $order = app(OrderService::class)->place([
        'customer_name' => 'Test Farmer',
        'customer_phone' => '03000000000',
        'shipping_address' => 'Village Road',
        'payment_method' => 'cash_on_delivery',
        'items' => [['product_id' => $product->id, 'quantity' => 2]],
    ]);

    expect($order->grand_total)->toBe('4400.00')
        ->and($order->items)->toHaveCount(1)
        ->and($product->fresh()->stock_quantity)->toBe(3);
});

test('paid magazines require a completed purchase', function () {
    $user = User::factory()->create();
    $magazine = Magazine::create([
        'title' => 'Kisan Monthly',
        'pdf_path' => 'magazines/kisan-monthly.pdf',
        'price' => 500,
    ]);
    $access = app(MagazineAccessService::class);

    expect($access->canAccess($user, $magazine))->toBeFalse();

    MagazinePurchase::create([
        'user_id' => $user->id,
        'magazine_id' => $magazine->id,
        'purchase_number' => 'KWM-TEST-1',
        'amount' => 500,
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    expect($access->canAccess($user, $magazine))->toBeTrue();
});

test('home loads products in batches of five', function () {
    $category = Category::create(['name' => 'Farm Inputs']);

    foreach (range(1, 11) as $number) {
        Product::create([
            'category_id' => $category->id,
            'name' => "Product {$number}",
            'price' => 1000 + $number,
            'stock_quantity' => 10,
        ]);
    }

    $this->get('/')
        ->assertOk()
        ->assertViewHas('products', fn ($products) => $products->count() === 5);

    $this->getJson('/home/products?page=2')
        ->assertOk()
        ->assertJsonPath('next_page_url', url('/home/products?page=3'))
        ->assertJson(fn ($json) => $json->whereType('html', 'string')->etc());
});

test('KISANWORLD content seeder is idempotent', function () {
    $this->seed(KisanWorldContentSeeder::class);
    $this->seed(KisanWorldContentSeeder::class);

    expect(Product::where('slug', 'mark6-fertilizer')->count())->toBe(1)
        ->and(Product::count())->toBe(11)
        ->and(ProductImage::count())->toBe(33)
        ->and(Blog::where('language', 'en')->count())->toBe(1)
        ->and(Blog::where('language', 'ur')->count())->toBe(1)
        ->and(Blog::where('slug', 'mark6-khad-zaraat-mein-naya-inqilab')->exists())->toBeTrue()
        ->and(Video::where('youtube_video_id', 'fSwKMJYrbY4')->count())->toBe(1)
        ->and(WebsiteSetting::where('key', 'whatsapp_url')->count())->toBe(1);
});

test('AJAX cart additions return real-time cart state', function () {
    $category = Category::create(['name' => 'Fertilizers']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Real Time Product',
        'price' => 1500,
        'stock_quantity' => 10,
    ]);

    $this->postJson(route('cart.store', $product), ['quantity' => 2])
        ->assertOk()
        ->assertJson([
            'cart_count' => 2,
            'item_quantity' => 2,
            'message' => 'Product added to cart.',
        ]);

    expect(session('cart.'.$product->id))->toBe(2);
});

test('product and cart pages expose all product images for rotation', function () {
    $category = Category::create(['name' => 'Fertilizers']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Multi Image Product',
        'price' => 2000,
        'stock_quantity' => 10,
        'description' => str_repeat('Long product description. ', 200),
    ]);

    foreach (range(1, 3) as $number) {
        ProductImage::create([
            'product_id' => $product->id,
            'path' => "products/image-{$number}.jpg",
            'alt_text' => "Product image {$number}",
            'sort_order' => $number,
            'is_primary' => $number === 1,
        ]);
    }

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('data-gallery-images', false)
        ->assertSee('products/image-3.jpg', false)
        ->assertSee(str_repeat('Long product description. ', 20), false);

    $this->withSession(['cart' => [$product->id => 1]])
        ->get(route('cart.index'))
        ->assertOk()
        ->assertSee('data-interval="10000"', false)
        ->assertSee('image-3.jpg', false);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('data-gallery-prev', false)
        ->assertSee('data-gallery-next', false)
        ->assertSee('data-interval="10000"', false);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('data-card-prev', false)
        ->assertSee('data-card-next', false)
        ->assertSee('data-interval="10000"', false);
});

test('product detail page uses ajax cart submit button markup', function () {
    $category = Category::create(['name' => 'Fertilizers']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Detail Ajax Product',
        'price' => 1800,
        'stock_quantity' => 10,
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('data-ajax-cart', false)
        ->assertSee('type="submit" class="button button-primary"', false);
});
