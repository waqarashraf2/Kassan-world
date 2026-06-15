@extends('layouts.admin')
@section('title', 'Conversation')
@section('heading', 'Live Chat')
@section('content')
<div class="admin-page-heading"><div><h1>{{ $conversation->customer_name ?: $conversation->user?->name ?: 'Website visitor' }}</h1><p>{{ $conversation->customer_email ?: $conversation->user?->email }} · {{ ucfirst($conversation->mode) }} mode</p></div><div class="admin-actions"><form action="{{ route('admin.chats.takeover', $conversation) }}" method="POST">@csrf<button class="admin-primary">Take over</button></form><form action="{{ route('admin.chats.close', $conversation) }}" method="POST">@csrf<button class="admin-secondary">Close chat</button></form></div></div>
<section class="admin-chat-card" data-admin-chat data-presence-url="{{ route('admin.chats.presence') }}" data-messages-url="{{ route('admin.chats.messages', $conversation) }}" data-last-message="{{ $conversation->messages->max('id') ?: 0 }}">
    <div class="admin-chat-messages" data-admin-chat-messages>@foreach($conversation->messages as $message)<div class="{{ $message->sender_type }}" data-message-id="{{ $message->id }}"><strong>{{ ucfirst($message->sender_type) }}</strong><p>{{ $message->message }}</p><small>{{ $message->created_at->format('M d, h:i A') }}</small></div>@endforeach</div>
    <form action="{{ route('admin.chats.reply', $conversation) }}" method="POST" class="admin-chat-reply">@csrf<textarea name="message" rows="3" placeholder="Write a helpful reply..." required></textarea><button class="admin-primary">Send reply</button></form>
</section>
@endsection
