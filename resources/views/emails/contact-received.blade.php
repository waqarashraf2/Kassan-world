@extends('emails.layout')
@section('content')
<p style="margin:0 0 8px;color:#f58220;font-size:12px;font-weight:bold;text-transform:uppercase">Message received</p>
<h1 style="margin:0 0 14px">Thank you, {{ $contact->name }}.</h1>
<p style="line-height:1.7;color:#53665b">Our support team has received your inquiry and will respond as soon as possible. Keep this email for your records.</p>
<blockquote style="margin:22px 0;padding:16px;border-left:4px solid #f58220;background:#f7faf8;color:#53665b">{{ $contact->message }}</blockquote>
@endsection
