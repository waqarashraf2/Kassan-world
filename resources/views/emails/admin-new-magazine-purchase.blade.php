@extends('emails.layout')
@section('content')
<p style="margin:0 0 8px;color:#f58220;font-size:12px;font-weight:bold;text-transform:uppercase">New magazine purchase</p>
<h1 style="margin:0 0 18px">{{ $purchase->purchase_number }}</h1>
<p style="line-height:1.7"><strong>{{ $purchase->user?->name }}</strong><br>{{ $purchase->user?->email }}<br>{{ $purchase->user?->phone }}</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;border:1px solid #dfe8e2">
<tr><td style="border-bottom:1px solid #edf2ee">Magazine</td><td align="right" style="border-bottom:1px solid #edf2ee"><strong>{{ $purchase->magazine?->title }}</strong></td></tr>
<tr><td style="border-bottom:1px solid #edf2ee">Amount</td><td align="right" style="border-bottom:1px solid #edf2ee">Rs. {{ number_format((float) $purchase->amount) }}</td></tr>
<tr><td style="border-bottom:1px solid #edf2ee">Payment method</td><td align="right" style="border-bottom:1px solid #edf2ee">{{ str($purchase->payment_method)->replace('_',' ')->title() }}</td></tr>
<tr><td>Status</td><td align="right">{{ ucfirst($purchase->payment_status) }}</td></tr>
</table>
@if($purchase->payment_proof_path)
<p style="margin:18px 0 0;line-height:1.7">Bank transfer proof was uploaded and is available inside the admin panel.</p>
@endif
<p style="margin:25px 0 0"><a href="{{ route('admin.magazine-purchases.index') }}" style="display:inline-block;padding:13px 20px;border-radius:9px;background:#0d6b3b;color:#fff;text-decoration:none;font-weight:bold">Open magazine sales</a></p>
@endsection
