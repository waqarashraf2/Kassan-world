<?php

use App\Models\Blog;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\KisanWorldContentSeeder;

test('admin login page is available', function () {
    $this->get(route('admin.login'))->assertOk()->assertSee('Secure Administration');
});

test('administrator can open all primary admin pages', function () {
    $this->seed(KisanWorldContentSeeder::class);
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::firstOrFail();
    $category = Category::firstOrFail();
    $blog = Blog::firstOrFail();

    $routes = [
        route('admin.dashboard'),
        route('admin.products.index'),
        route('admin.products.create'),
        route('admin.products.edit', $product),
        route('admin.categories.index'),
        route('admin.categories.create'),
        route('admin.categories.edit', $category),
        route('admin.blogs.index'),
        route('admin.blogs.create'),
        route('admin.blogs.edit', $blog),
        route('admin.blog-categories.index'),
        route('admin.blog-categories.create'),
        route('admin.videos.index'),
        route('admin.videos.create'),
        route('admin.magazines.index'),
        route('admin.magazines.create'),
        route('admin.orders.index'),
        route('admin.magazine-purchases.index'),
        route('admin.contacts.index'),
        route('admin.chats.index'),
        route('admin.chatbot-faqs.index'),
        route('admin.support-tickets.index'),
        route('admin.settings.edit'),
    ];

    foreach ($routes as $url) {
        $this->actingAs($admin)->get($url)->assertOk();
    }
});
