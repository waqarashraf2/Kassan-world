@extends('layouts.app')
@section('title', 'Order Confirmed | KISANWORLD')
@section('content')
<section class="success-page">
    <div>
        <span aria-hidden="true">&#10003;</span>
        <p class="section-kicker">Order received</p>
        <h1>Thank you for your order.</h1>
        <p>Your order number is <strong>{{ $order->order_number }}</strong>. Our team will contact you to confirm delivery.</p>
        @auth
            <a href="{{ route('customer.orders.show', $order) }}" class="button button-primary">Track order</a>
        @else
            <a href="{{ route('products.index') }}" class="button button-primary">Continue shopping</a>
        @endauth
    </div>
</section>
@endsection
