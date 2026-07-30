@extends('layouts.admin')
@section('title', 'Magazine Sales')
@section('heading', 'Magazine Sales')
@section('content')
<div class="admin-page-heading"><div><h1>Magazine purchases</h1><p>Approve payments to unlock secure PDF access.</p></div></div>
<section class="admin-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Purchase</th><th>Customer</th><th>Magazine</th><th>Amount</th><th>Method</th><th>Proof</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($purchases as $purchase)<tr><td><strong>{{ $purchase->purchase_number }}</strong><small>{{ $purchase->created_at->format('M d, Y') }}</small></td><td>{{ $purchase->user?->name }}<small>{{ $purchase->user?->email }}</small></td><td>{{ $purchase->magazine?->title }}</td><td>Rs. {{ number_format((float)$purchase->amount) }}</td><td>{{ str($purchase->payment_method)->replace('_',' ')->title() }}</td><td>@if($purchase->payment_proof_path)<a href="{{ route('admin.magazine-purchases.payment-proof',$purchase) }}" target="_blank">View proof</a>@else<span class="admin-muted">Not required</span>@endif</td><td><span class="admin-status {{ $purchase->payment_status }}">{{ ucfirst($purchase->payment_status) }}</span></td><td><form class="admin-inline-form" action="{{ route('admin.magazine-purchases.update',$purchase) }}" method="POST">@csrf @method('PATCH')<input name="payment_reference" value="{{ $purchase->payment_reference }}" placeholder="Reference"><select name="payment_status">@foreach(['pending','paid','failed','refunded'] as $status)<option value="{{ $status }}" @selected($purchase->payment_status===$status)>{{ ucfirst($status) }}</option>@endforeach</select><button class="admin-primary">Save</button></form></td></tr>
@empty<tr><td colspan="8" class="admin-empty">No magazine purchases found.</td></tr>@endforelse
</tbody></table></div><div class="admin-pagination">{{ $purchases->links() }}</div></section>
@endsection
