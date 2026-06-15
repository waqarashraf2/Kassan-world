@extends('emails.layout')
@section('content')
<p style="margin:0 0 8px;color:#f58220;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em">{{ $headline }}</p>
<h1 style="margin:0 0 14px;font-size:28px">{{ $order->order_number }}</h1>
<p style="margin:0 0 22px;line-height:1.7;color:#53665b">Hello {{ $order->customer_name }}, {{ $statusMessage }}</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;border:1px solid #dfe8e2">
@foreach($order->items as $item)<tr><td style="border-bottom:1px solid #edf2ee">{{ $item->product_name }} x {{ $item->quantity }}</td><td align="right" style="border-bottom:1px solid #edf2ee">Rs. {{ number_format((float) $item->line_total) }}</td></tr>@endforeach
<tr><td><strong>Total</strong></td><td align="right"><strong>Rs. {{ number_format((float) $order->grand_total) }}</strong></td></tr>
</table>
@if($order->user_id)<p style="margin:25px 0 0"><a href="{{ route('customer.orders.show', $order) }}" style="display:inline-block;padding:13px 20px;border-radius:9px;background:#0d6b3b;color:#fff;text-decoration:none;font-weight:bold">Track your order</a></p>@endif
@endsection
