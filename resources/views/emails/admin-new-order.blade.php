@extends('emails.layout')
@section('content')
<p style="margin:0 0 8px;color:#f58220;font-size:12px;font-weight:bold;text-transform:uppercase">New order</p>
<h1 style="margin:0 0 18px">{{ $order->order_number }}</h1>
<p style="line-height:1.7"><strong>{{ $order->customer_name }}</strong><br>{{ $order->customer_phone }}<br>{{ $order->customer_email }}<br>{{ $order->shipping_address }}, {{ $order->city }}</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;border:1px solid #dfe8e2">
@foreach($order->items as $item)<tr><td style="border-bottom:1px solid #edf2ee">{{ $item->product_name }} x {{ $item->quantity }}</td><td align="right" style="border-bottom:1px solid #edf2ee">Rs. {{ number_format((float) $item->line_total) }}</td></tr>@endforeach
<tr><td><strong>Order value</strong></td><td align="right"><strong>Rs. {{ number_format((float) $order->grand_total) }}</strong></td></tr>
</table>
<p style="margin:18px 0 0;line-height:1.7"><strong>Payment method:</strong> {{ str($order->payment_method)->replace('_',' ')->title() }}<br><strong>Payment status:</strong> {{ ucfirst($order->payment_status) }}</p>
@if($order->payment_proof_path)
<p style="margin:18px 0 0;line-height:1.7">Bank transfer proof was uploaded and is available inside the admin panel.</p>
@endif
<p style="margin:25px 0 0"><a href="{{ route('admin.orders.show', $order) }}" style="display:inline-block;padding:13px 20px;border-radius:9px;background:#0d6b3b;color:#fff;text-decoration:none;font-weight:bold">Open in admin panel</a></p>
@endsection
