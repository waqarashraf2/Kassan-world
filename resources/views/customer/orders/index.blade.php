@extends('layouts.app')
@section('title', 'Order History | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Order history" title="Every order, clearly tracked" />
<x-customer-shell title="Order history">
    <section class="customer-card">
        <div class="customer-order-list customer-order-history">
            @forelse($orders as $order)
            <a href="{{ route('customer.orders.show', $order) }}"><div><strong>{{ $order->order_number }}</strong><small>{{ $order->placed_at?->format('F d, Y') }} · {{ $order->items_count }} item(s) · {{ ucfirst(str_replace('_',' ',$order->payment_method)) }}</small></div><span class="order-pill {{ $order->status }}">{{ ucfirst($order->status === 'completed' ? 'delivered' : $order->status) }}</span><b>Rs. {{ number_format((float)$order->grand_total) }}</b></a>
            @empty<div class="customer-empty">No order history is available yet.</div>@endforelse
        </div>
        <div class="pagination-wrap">{{ $orders->links() }}</div>
    </section>
</x-customer-shell>
@endsection
