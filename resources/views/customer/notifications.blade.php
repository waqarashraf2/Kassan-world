@extends('layouts.app')
@section('title', 'Notifications | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Account updates" title="Notification center" />
<x-customer-shell title="Notifications">
    <section class="customer-card">
        <div class="customer-card-head"><div><h2>All notifications</h2><p>Order and account activity.</p></div><form action="{{ route('customer.notifications.read') }}" method="POST">@csrf<button class="text-button">Mark all read</button></form></div>
        <div class="notification-list full">@forelse($notifications as $notification)<a href="{{ $notification->data['url'] ?? '#' }}" class="{{ $notification->read_at ? '' : 'unread' }}"><span></span><p><strong>{{ $notification->data['order_number'] ?? 'KISANWORLD' }}</strong>{{ $notification->data['message'] ?? 'Account update' }}<small>{{ $notification->created_at->diffForHumans() }}</small></p></a>@empty<div class="customer-empty">No notifications yet.</div>@endforelse</div>
        <div class="pagination-wrap">{{ $notifications->links() }}</div>
    </section>
</x-customer-shell>
@endsection
