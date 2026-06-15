@extends('layouts.admin')
@section('title', $ticket->ticket_number)
@section('heading', 'Support Ticket')
@section('content')
<div class="admin-page-heading"><div><h1>{{ $ticket->ticket_number }}</h1><p>{{ $ticket->subject }}</p></div><a class="admin-secondary" href="{{ route('admin.support-tickets.index') }}">Back</a></div>
<div class="admin-detail-grid"><section class="admin-card admin-message"><div class="admin-contact-meta"><strong>{{ $ticket->name }}</strong><a href="mailto:{{ $ticket->email }}">{{ $ticket->email }}</a></div><p>{{ $ticket->message }}</p></section>
<form class="admin-form-card" action="{{ route('admin.support-tickets.update', $ticket) }}" method="POST">@csrf @method('PUT')<h2>Manage ticket</h2><label>Status<select name="status">@foreach(['open','in_progress','resolved','closed'] as $status)<option value="{{ $status }}" @selected($ticket->status===$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>@endforeach</select></label><label>Priority<select name="priority">@foreach(['low','normal','high'] as $priority)<option value="{{ $priority }}" @selected($ticket->priority===$priority)>{{ ucfirst($priority) }}</option>@endforeach</select></label><button class="admin-primary">Update ticket</button></form></div>
@endsection
