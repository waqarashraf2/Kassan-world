@extends('layouts.app')
@section('title', ($magazine->meta_title ?: $magazine->title).' | KISANWORLD')
@section('meta_description', $magazine->meta_description ?: $magazine->description)
@section('canonical', $magazine->canonical_url ?: route('magazines.show',$magazine))
@section('content')
<x-page-hero eyebrow="{{ $magazine->is_free ? 'Free magazine' : 'Premium magazine' }}" title="{{ $magazine->title }}" />
<section class="inner-section section-shell magazine-detail">
    <img src="{{ $magazine->cover_image ? asset(ltrim($magazine->cover_image,'/')) : asset('logos and images/Kisaan world-transparent.png') }}" alt="{{ $magazine->cover_image_alt ?: $magazine->title }}">
    <div>
        <div class="detail-price"><strong>{{ $magazine->is_free ? 'Free' : 'Rs. '.number_format((float)$magazine->price) }}</strong></div>
        <p>{{ $magazine->description }}</p>
        @if($magazine->is_free || ($canAccess ?? false))
            <div class="hero-ctas">
                <a class="button button-primary" href="{{ route('magazines.read',$magazine) }}">Read online</a>
                @if($magazine->allow_download)<a class="button outline-dark" href="{{ route('magazines.download',$magazine) }}">Download PDF</a>@endif
            </div>
        @elseif($purchase ?? null)
            <div class="payment-status-box">
                <strong>Payment {{ ucfirst($purchase->payment_status) }}</strong>
                <p>Your PDF access will unlock after payment is verified. Bank transfer screenshots are reviewed from the admin panel.</p>
            </div>
        @else
        @auth
            <form action="{{ route('magazines.purchase',$magazine) }}" method="POST" enctype="multipart/form-data" class="magazine-purchase-form" data-payment-scope>
                @csrf
                <x-form-errors />
                <label>Payment method<select name="payment_method" data-payment-method><option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank transfer</option><option value="online_payment" @selected(old('payment_method') === 'online_payment')>Online payment - Bank Alfalah</option></select></label>
                <div class="bank-transfer-box" data-bank-transfer-fields @if(old('payment_method') === 'online_payment') hidden @endif>
                    <div><span>Bank transfer proof</span><p>Upload the screenshot or PDF receipt after sending payment. Admin will verify it before PDF access is unlocked.</p></div>
                    <label>Payment screenshot / receipt<input type="file" name="payment_proof" accept="image/jpeg,image/png,image/webp,application/pdf"></label>
                    <label>Reference number <input name="payment_reference" value="{{ old('payment_reference') }}" placeholder="Optional transaction ID"></label>
                </div>
                <div class="online-payment-box" data-online-payment-fields @if(old('payment_method') !== 'online_payment') hidden @endif>
                    <div><span>Secure online payment</span><p>Enter billing details only. Card number, CVV, PIN and OTP must be entered only on the approved bank payment page.</p></div>
                    <div class="form-grid"><label>Billing name<input name="billing_name" value="{{ old('billing_name', auth()->user()?->name) }}"></label><label>Billing email<input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()?->email) }}"></label></div>
                    <div class="form-grid"><label>Billing phone<input name="billing_phone" value="{{ old('billing_phone', auth()->user()?->phone) }}"></label><label>Billing city<input name="billing_city" value="{{ old('billing_city') }}"></label></div>
                    <label>Billing address<textarea name="billing_address" rows="3">{{ old('billing_address') }}</textarea></label>
                    <label class="check-label"><input type="checkbox" name="online_payment_consent" value="1" @checked(old('online_payment_consent'))> I understand card details will be entered only on the secure bank payment page.</label>
                </div>
                <button class="button button-primary">Submit payment details</button>
            </form>
        @else
            <a class="button button-primary" href="{{ route('login') }}">Login to purchase</a>
        @endauth
        @endif
    </div>
</section>
@endsection
