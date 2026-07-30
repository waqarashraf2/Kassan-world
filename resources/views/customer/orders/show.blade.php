@extends('layouts.app')
@section('title', $order->order_number.' | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Live order tracking" title="{{ $order->order_number }}" text="Placed {{ $order->placed_at?->format('F d, Y \a\t h:i A') }}" />
<x-customer-shell title="Order details">
    <section class="customer-card tracking-card">
        <div class="customer-card-head"><div><h2>Order progress</h2><p>Latest status: {{ ucfirst($order->status === 'completed' ? 'delivered' : $order->status) }}</p></div><span class="order-pill {{ $order->status }}">{{ ucfirst($order->status) }}</span></div>
        <ol class="order-timeline">
            @foreach($order->statusEvents as $event)<li class="{{ $loop->last ? 'current' : '' }}"><span></span><div><strong>{{ ucfirst($event->status === 'completed' ? 'Delivered' : $event->status) }}</strong><p>{{ $event->note ?: 'Your order status was updated.' }}</p><small>{{ $event->occurred_at?->format('M d, Y · h:i A') }}</small></div></li>@endforeach
        </ol>
    </section>
    <div class="customer-dashboard-grid order-detail-grid">
        <section class="customer-card">
            <div class="customer-card-head"><div><h2>Products</h2></div></div>
            <div class="order-items">@foreach($order->items as $item)<div>@if($item->product?->images->first())<img src="{{ $item->product->images->first()->url }}" alt="{{ $item->product_name }}">@endif<div><strong>{{ $item->product_name }}</strong><small>{{ $item->quantity }} x Rs. {{ number_format((float)$item->unit_price) }}</small></div><b>Rs. {{ number_format((float)$item->line_total) }}</b></div>@endforeach</div>
            <div class="order-total"><span>Total</span><strong>Rs. {{ number_format((float)$order->grand_total) }}</strong></div>
        </section>
        <section class="customer-card order-address"><div class="customer-card-head"><div><h2>Delivery details</h2></div></div><p><strong>{{ $order->customer_name }}</strong><br>{{ $order->customer_phone }}<br>{{ $order->customer_email }}<br><br>{{ $order->shipping_address }}<br>{{ $order->city }}</p><hr><p>Payment: <strong>{{ ucfirst(str_replace('_',' ',$order->payment_method)) }}</strong><br>Status: <strong>{{ ucfirst($order->payment_status) }}</strong></p>@if($order->payment_method === 'bank_transfer' && $order->payment_proof_path)<p class="payment-safe-note">Your bank transfer proof was uploaded for admin verification.</p>@endif @if($order->payment_method === 'online_payment')<p class="payment-safe-note">Online card details are completed on the secure bank payment page only. KISANWORLD does not store card number, CVV, PIN, OTP or gateway secrets.</p>@endif</section>
    </div>
</x-customer-shell>
@endsection
