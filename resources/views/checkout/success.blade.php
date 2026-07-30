@extends('layouts.app')
@section('title', 'Order Confirmed | KISANWORLD')
@section('content')
<section class="success-page">
    <div>
        <span aria-hidden="true">&#10003;</span>
        <p class="section-kicker">Order received</p>
        <h1>Thank you for your order.</h1>
        <p>Your order number is <strong>{{ $order->order_number }}</strong>. Our team will contact you to confirm delivery.</p>
        @if($order->payment_method === 'bank_transfer')
            <p>Your bank transfer proof has been received and will be verified by our team.</p>
        @elseif($order->payment_method === 'online_payment')
            <p>Online card details must be entered only on the secure bank payment page. KISANWORLD does not store card numbers, CVV, PIN or OTP.</p>
        @endif
        @auth
            <a href="{{ route('customer.orders.show', $order) }}" class="button button-primary">Track order</a>
        @else
            <a href="{{ route('products.index') }}" class="button button-primary">Continue shopping</a>
        @endauth
    </div>
</section>
@endsection
