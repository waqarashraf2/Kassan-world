<?php

use App\Mail\NewOrderAdminMail;
use App\Mail\OrderStatusMail;
use App\Models\Category;
use App\Models\ChatbotFaq;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\ChatbotService;
use Database\Seeders\ChatbotFaqSeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

test('customer can register and open the account dashboard', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Customer Farmer',
        'email' => 'farmer@example.com',
        'phone' => '03001234567',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ]);

    $response->assertRedirect(route('customer.dashboard'));
    $this->assertAuthenticated();
    $this->get(route('customer.dashboard'))->assertOk()->assertSee('Account overview');
});

test('guest can create an account inside checkout without interrupting the order', function () {
    Mail::fake();
    $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
    $category = Category::create(['name' => 'Checkout Products']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Checkout Fertilizer',
        'price' => 2500,
        'stock_quantity' => 10,
    ]);

    $response = $this->withSession(['cart' => [$product->id => 2]])->post(route('checkout.store'), [
        'customer_name' => 'Guest Farmer',
        'customer_email' => 'guest@example.com',
        'customer_phone' => '03000000000',
        'shipping_address' => 'Farm Road, Punjab',
        'city' => 'Lahore',
        'payment_method' => 'cash_on_delivery',
        'create_account' => '1',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
        'save_address' => '1',
        'items' => [['product_id' => $product->id, 'quantity' => 2]],
    ]);

    $user = User::where('email', 'guest@example.com')->firstOrFail();
    $order = Order::where('user_id', $user->id)->firstOrFail();

    $response->assertRedirect(route('checkout.success', $order->order_number));
    $this->assertAuthenticatedAs($user);
    expect($order->items)->toHaveCount(1)
        ->and($order->statusEvents)->toHaveCount(1);
    Mail::assertQueued(OrderStatusMail::class);
    Mail::assertQueued(NewOrderAdminMail::class, fn ($mail) => $mail->hasTo($admin->email));
});

test('FAQ seeder creates one thousand unique searchable questions', function () {
    $this->seed(ChatbotFaqSeeder::class);
    $this->seed(ChatbotFaqSeeder::class);

    expect(ChatbotFaq::count())->toBe(1000)
        ->and(ChatbotFaq::distinct('question')->count('question'))->toBe(1000);

    $visitor = (string) Str::uuid();
    $this->postJson(route('chat.message'), [
        'visitor_token' => $visitor,
        'message' => 'How can I track my order?',
    ])->assertOk()
        ->assertJsonPath('reply.sender', 'bot')
        ->assertJsonStructure(['conversation_id', 'reply' => ['id', 'message']]);
});

test('customers cannot open another customers order', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $order = Order::create([
        'order_number' => 'KW-PRIVATE-1',
        'user_id' => $owner->id,
        'customer_name' => $owner->name,
        'customer_email' => $owner->email,
        'customer_phone' => '03000000000',
        'shipping_address' => 'Private address',
        'subtotal' => 100,
        'grand_total' => 100,
        'placed_at' => now(),
    ]);

    $this->actingAs($other)->get(route('customer.orders.show', $order))->assertNotFound();
});

test('chatbot can answer from live product data', function () {
    $category = Category::create(['name' => 'Live Chat Products']);
    Product::create([
        'category_id' => $category->id,
        'name' => 'Mark6 Fertilizer',
        'price' => 3200,
        'stock_quantity' => 5,
    ]);

    $answer = app(ChatbotService::class)->answer('What is the price of Mark6 Fertilizer?');

    expect($answer['matched'])->toBeTrue()
        ->and($answer['answer'])->toContain('Rs. 3,200')
        ->and($answer['answer'])->toContain('in stock');
});
