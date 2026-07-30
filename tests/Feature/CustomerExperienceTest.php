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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
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

test('registration claims previous guest orders with the same email', function () {
    $order = Order::create([
        'order_number' => 'KW-GUEST-REGISTER-1',
        'customer_name' => 'Guest Register',
        'customer_email' => 'claim-register@example.com',
        'customer_phone' => '03000000000',
        'shipping_address' => 'Old guest farm address',
        'subtotal' => 1500,
        'grand_total' => 1500,
        'placed_at' => now()->subDay(),
    ]);

    $this->post(route('register.store'), [
        'name' => 'Claim Register',
        'email' => 'claim-register@example.com',
        'phone' => '03001234567',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ])->assertRedirect(route('customer.dashboard'));

    $user = User::where('email', 'claim-register@example.com')->firstOrFail();

    expect($order->fresh()->user_id)->toBe($user->id);
    $this->actingAs($user)
        ->get(route('customer.orders.index'))
        ->assertOk()
        ->assertSee('KW-GUEST-REGISTER-1');
});

test('login claims previous guest orders with the same email', function () {
    $user = User::factory()->create([
        'email' => 'claim-login@example.com',
        'password' => Hash::make('secure-password'),
    ]);
    $order = Order::create([
        'order_number' => 'KW-GUEST-LOGIN-1',
        'customer_name' => 'Guest Login',
        'customer_email' => 'claim-login@example.com',
        'customer_phone' => '03000000000',
        'shipping_address' => 'Previous checkout address',
        'subtotal' => 1800,
        'grand_total' => 1800,
        'placed_at' => now()->subDay(),
    ]);

    $this->post(route('login.store'), [
        'email' => 'claim-login@example.com',
        'password' => 'secure-password',
    ])->assertRedirect(route('customer.dashboard'));

    expect($order->fresh()->user_id)->toBe($user->id);
    $this->actingAs($user)
        ->get(route('customer.orders.index'))
        ->assertOk()
        ->assertSee('KW-GUEST-LOGIN-1');
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

    $response->assertSessionHasNoErrors();
    $user = User::where('email', 'guest@example.com')->firstOrFail();
    $order = Order::where('user_id', $user->id)->firstOrFail();

    $response->assertRedirect(route('checkout.success', $order->order_number));
    $this->assertAuthenticatedAs($user);
    expect($order->items)->toHaveCount(1)
        ->and($order->statusEvents)->toHaveCount(1);
    Mail::assertSent(OrderStatusMail::class);
    Mail::assertSent(NewOrderAdminMail::class, fn ($mail) => $mail->hasTo($admin->email));
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

test('checkout stores safe online payment billing details without card data', function () {
    Mail::fake();
    $category = Category::create(['name' => 'Online Payment Products']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Online Payment Fertilizer',
        'price' => 3000,
        'stock_quantity' => 6,
    ]);

    $response = $this->withSession(['cart' => [$product->id => 1]])->post(route('checkout.store'), [
        'customer_name' => 'Online Farmer',
        'customer_email' => 'online@example.com',
        'customer_phone' => '03001112222',
        'shipping_address' => 'Farm Road',
        'city' => 'Lahore',
        'payment_method' => 'online_payment',
        'billing_name' => 'Online Farmer',
        'billing_email' => 'online@example.com',
        'billing_phone' => '03001112222',
        'billing_city' => 'Lahore',
        'billing_address' => 'Farm Road',
        'online_payment_consent' => '1',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);

    $order = Order::where('customer_email', 'online@example.com')->firstOrFail();

    $response->assertRedirect(route('checkout.success', $order->order_number));
    expect($order->payment_method)->toBe('online_payment')
        ->and($order->payment_details['gateway'])->toBe('bank_alfalah')
        ->and($order->payment_details['card_collection'])->toBe('redirect_gateway_only')
        ->and($order->payment_details)->not->toHaveKey('card_number');
});

test('checkout rejects raw card fields on the merchant server', function () {
    $category = Category::create(['name' => 'Secure Payment Products']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Secure Payment Fertilizer',
        'price' => 3000,
        'stock_quantity' => 6,
    ]);

    $this->withSession(['cart' => [$product->id => 1]])->post(route('checkout.store'), [
        'customer_name' => 'Card Farmer',
        'customer_email' => 'card@example.com',
        'customer_phone' => '03001112222',
        'shipping_address' => 'Farm Road',
        'city' => 'Lahore',
        'payment_method' => 'online_payment',
        'billing_name' => 'Card Farmer',
        'billing_email' => 'card@example.com',
        'billing_phone' => '03001112222',
        'billing_address' => 'Farm Road',
        'online_payment_consent' => '1',
        'card_number' => '4111111111111111',
        'cvc' => '123',
        'expiry_date' => '12/30',
    ])->assertSessionHasErrors(['card_number', 'cvc', 'expiry_date']);

    expect(Order::where('customer_email', 'card@example.com')->exists())->toBeFalse();
});

test('bank transfer checkout requires and stores private payment proof', function () {
    Storage::fake('local');
    Mail::fake();
    $category = Category::create(['name' => 'Bank Transfer Products']);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Bank Transfer Fertilizer',
        'price' => 3200,
        'stock_quantity' => 6,
    ]);

    $this->withSession(['cart' => [$product->id => 1]])->post(route('checkout.store'), [
        'customer_name' => 'Bank Farmer',
        'customer_email' => 'bank@example.com',
        'customer_phone' => '03001112222',
        'shipping_address' => 'Farm Road',
        'city' => 'Lahore',
        'payment_method' => 'bank_transfer',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ])->assertSessionHasErrors(['payment_proof']);

    $response = $this->withSession(['cart' => [$product->id => 1]])->post(route('checkout.store'), [
        'customer_name' => 'Bank Farmer',
        'customer_email' => 'bank@example.com',
        'customer_phone' => '03001112222',
        'shipping_address' => 'Farm Road',
        'city' => 'Lahore',
        'payment_method' => 'bank_transfer',
        'payment_proof' => UploadedFile::fake()->image('receipt.jpg'),
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);

    $order = Order::where('customer_email', 'bank@example.com')->firstOrFail();

    $response->assertRedirect(route('checkout.success', $order->order_number));
    expect($order->payment_proof_path)->not->toBeNull();
    Storage::disk('local')->assertExists($order->payment_proof_path);
});
