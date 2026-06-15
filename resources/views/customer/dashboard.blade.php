@extends('layouts.app')
@section('title', 'My Account | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Customer dashboard" title="Welcome, {{ auth()->user()->name }}" text="Your orders, saved details and support in one clear place." />
<x-customer-shell title="Account overview">
    <div class="customer-stat-grid">
        <article><span>Total orders</span><strong>{{ $orderCounts->sum() }}</strong></article>
        <article><span>In progress</span><strong>{{ ($orderCounts['confirmed'] ?? 0) + ($orderCounts['processing'] ?? 0) + ($orderCounts['shipped'] ?? 0) }}</strong></article>
        <article><span>Delivered</span><strong>{{ $orderCounts['completed'] ?? 0 }}</strong></article>
        <article><span>Saved items</span><strong>{{ auth()->user()->wishlistProducts()->count() }}</strong></article>
    </div>
    <section class="customer-card">
        <div class="customer-card-head"><div><h2>Recent orders</h2><p>Live status from order received through delivery.</p></div><a href="{{ route('customer.orders.index') }}">View all</a></div>
        <div class="customer-order-list">
            @forelse($recentOrders as $order)
                <a href="{{ route('customer.orders.show', $order) }}"><div><strong>{{ $order->order_number }}</strong><small>{{ $order->placed_at?->format('M d, Y') }} · {{ $order->items->count() }} item(s)</small></div><span class="order-pill {{ $order->status }}">{{ ucfirst($order->status === 'completed' ? 'delivered' : $order->status) }}</span><b>Rs. {{ number_format((float)$order->grand_total) }}</b></a>
            @empty
                <div class="customer-empty">No orders yet. <a href="{{ route('products.index') }}">Explore products</a></div>
            @endforelse
        </div>
    </section>
    <div class="customer-dashboard-grid">
        <section class="customer-card">
            <div class="customer-card-head"><div><h2>Notifications</h2><p>Important account and order updates.</p></div><a href="{{ route('customer.notifications.index') }}">Open center</a></div>
            <div class="notification-list">@forelse($unreadNotifications as $notification)<div><span></span><p>{{ $notification->data['message'] ?? 'Account update' }}<small>{{ $notification->created_at->diffForHumans() }}</small></p></div>@empty<div class="customer-empty">You are all caught up.</div>@endforelse</div>
        </section>
        <section class="customer-card support-card">
            <div class="customer-card-head"><div><h2>Need support?</h2><p>Create a ticket and receive an email confirmation.</p></div></div>
            <form action="{{ route('support-tickets.store') }}" method="POST">@csrf
                <input type="hidden" name="name" value="{{ auth()->user()->name }}"><input type="hidden" name="email" value="{{ auth()->user()->email }}">
                <label>Subject<input name="subject" required></label><label>Message<textarea name="message" rows="3" required></textarea></label>
                <button class="button button-primary">Create support ticket</button>
            </form>
        </section>
    </div>
</x-customer-shell>
@endsection
