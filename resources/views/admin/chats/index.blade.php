@extends('layouts.admin')
@section('title', 'Live Chat')
@section('heading', 'Live Chat')
@section('content')
<div class="admin-page-heading"><div><h1>Customer conversations</h1><p>You are marked online while this panel is open. Manual replies take priority over the FAQ assistant.</p></div><span class="admin-status paid">Online</span></div>
<section class="admin-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Customer</th><th>Mode</th><th>Status</th><th>Messages</th><th>Last activity</th><th></th></tr></thead><tbody>
@forelse($conversations as $conversation)<tr><td><strong>{{ $conversation->customer_name ?: $conversation->user?->name ?: 'Website visitor' }}</strong><small>{{ $conversation->customer_email ?: $conversation->user?->email }}</small></td><td><span class="admin-status {{ $conversation->mode === 'live' ? 'paid' : 'pending' }}">{{ ucfirst($conversation->mode) }}</span></td><td>{{ ucfirst($conversation->status) }}</td><td>{{ $conversation->messages_count }}</td><td>{{ $conversation->last_message_at?->diffForHumans() }}</td><td><a href="{{ route('admin.chats.show', $conversation) }}">Open</a></td></tr>
@empty<tr><td colspan="6" class="admin-empty">No conversations yet.</td></tr>@endforelse
</tbody></table></div><div class="admin-pagination">{{ $conversations->links() }}</div></section>
@endsection
