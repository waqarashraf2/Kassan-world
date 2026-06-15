@extends('layouts.admin')
@section('title', 'Support Tickets')
@section('heading', 'Support Tickets')
@section('content')
<div class="admin-page-heading"><div><h1>Support tickets</h1><p>Customer requests created from the account dashboard.</p></div></div>
<section class="admin-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Ticket</th><th>Customer</th><th>Subject</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr></thead><tbody>
@forelse($tickets as $ticket)<tr><td><strong>{{ $ticket->ticket_number }}</strong></td><td>{{ $ticket->name }}<small>{{ $ticket->email }}</small></td><td>{{ $ticket->subject }}</td><td>{{ ucfirst($ticket->priority) }}</td><td><span class="admin-status {{ $ticket->status === 'resolved' ? 'paid' : 'pending' }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span></td><td>{{ $ticket->created_at->format('M d, Y') }}</td><td><a href="{{ route('admin.support-tickets.show', $ticket) }}">Open</a></td></tr>
@empty<tr><td colspan="7" class="admin-empty">No support tickets.</td></tr>@endforelse
</tbody></table></div><div class="admin-pagination">{{ $tickets->links() }}</div></section>
@endsection
