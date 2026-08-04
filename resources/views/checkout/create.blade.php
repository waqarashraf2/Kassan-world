@extends('layouts.app')
@section('title', 'Checkout | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Secure checkout" title="Place your order" text="Enter delivery details and confirm your selected products." />
<section class="inner-section section-shell checkout-layout">
    @if($products->isEmpty())<div class="empty-state"><strong>Your cart is empty.</strong><a href="{{ route('products.index') }}">Shop products →</a></div>
    @else
    <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" class="site-form checkout-form" data-payment-scope>@csrf
        <x-form-errors />
        <div class="form-grid"><label>Name<input name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" required></label><label>Phone<input name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" required></label></div>
        <label>Email<input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}"></label>
        <label>Address<textarea name="shipping_address" rows="4" required>{{ old('shipping_address') }}</textarea></label>
        <div class="form-grid"><label>City<input name="city" value="{{ old('city') }}"></label><label>Payment<select name="payment_method" data-payment-method><option value="cash_on_delivery" @selected(old('payment_method') === 'cash_on_delivery')>Cash on delivery</option><option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank transfer</option><option value="online_payment" @selected(old('payment_method') === 'online_payment')>Online payment - Bank Alfalah</option></select></label></div>
        <div class="online-payment-box" data-online-payment-fields @if(old('payment_method') !== 'online_payment') hidden @endif>
            <div><span>Bank Alfalah online payment readiness</span><p>These are billing details only. Card number, CVV, PIN, OTP and gateway secret keys must be collected only on Bank Alfalah's secure approved gateway.</p></div>
            <div class="form-grid"><label>Billing name<input name="billing_name" value="{{ old('billing_name', auth()->user()?->name) }}"></label><label>Billing email<input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()?->email) }}"></label></div>
            <div class="form-grid"><label>Billing phone<input name="billing_phone" value="{{ old('billing_phone', auth()->user()?->phone) }}"></label><label>Billing city<input name="billing_city" value="{{ old('billing_city') }}"></label></div>
            <label>Billing address<textarea name="billing_address" rows="3">{{ old('billing_address') }}</textarea></label>
            <label class="check-label"><input type="checkbox" name="online_payment_consent" value="1" @checked(old('online_payment_consent'))> I understand card details will be entered only on the secure bank payment page after approval.</label>
        </div>
        <div class="bank-transfer-box" data-bank-transfer-fields @if(old('payment_method') !== 'bank_transfer') hidden @endif>
            <x-payment-account-details intro="Copy any account number below, send payment, then upload the payment screenshot or receipt with your order." />
            <label>Payment screenshot / receipt<input type="file" name="payment_proof" accept="image/jpeg,image/png,image/webp,application/pdf" data-payment-proof-input><small data-payment-proof-name>JPG, PNG, WEBP or PDF up to 4MB.</small></label>
        </div>
        <label>Order notes<textarea name="notes" rows="3">{{ old('notes') }}</textarea></label>
        @guest
        <div class="checkout-account" data-checkout-account>
            <label class="check-label"><input type="checkbox" name="create_account" value="1" @checked(old('create_account')) data-account-toggle> Create an account while placing this order</label>
            <div class="form-grid" data-account-fields @if(!old('create_account')) hidden @endif>
                <label>Password<input type="password" name="password" autocomplete="new-password" minlength="8"></label>
                <label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password"></label>
            </div>
            <small>You will stay on checkout, be signed in automatically, and this order will appear in your dashboard.</small>
        </div>
        @else
        <label class="check-label"><input type="checkbox" name="save_address" value="1" @checked(old('save_address', true))> Save this delivery address</label>
        @endguest
        @foreach($products as $index => $product)<input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $product->id }}"><input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $cart[$product->id] }}">@endforeach
        <button class="button button-primary">Place order</button>
    </form>
    <aside class="cart-summary"><h2>Order summary</h2>@foreach($products as $product)<div><span>{{ $product->name }} × {{ $cart[$product->id] }}</span><strong>Rs. {{ number_format((float)$product->sale_price * $cart[$product->id]) }}</strong></div>@endforeach<hr><div><span>Total</span><strong>Rs. {{ number_format($products->sum(fn($product)=>(float)$product->sale_price * $cart[$product->id])) }}</strong></div></aside>
    @endif
</section>
@endsection
