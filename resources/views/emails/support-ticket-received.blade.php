@extends('emails.layout')
@section('content')
<p style="margin:0 0 8px;color:#f58220;font-size:12px;font-weight:bold;text-transform:uppercase">Support ticket received</p>
<h1 style="margin:0 0 14px">{{ $ticket->ticket_number }}</h1>
<p style="line-height:1.7;color:#53665b">Hello {{ $ticket->name }}, our team has received your request about <strong>{{ $ticket->subject }}</strong>. We will reply as soon as possible.</p>
@endsection
