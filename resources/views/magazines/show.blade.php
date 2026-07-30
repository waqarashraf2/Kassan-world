@extends('layouts.app')
@section('title', ($magazine->meta_title ?: $magazine->title).' | KISANWORLD')
@section('meta_description', $magazine->meta_description ?: $magazine->description)
@section('canonical', $magazine->canonical_url ?: route('magazines.show',$magazine))
@section('content')
<x-page-hero eyebrow="{{ $magazine->is_free ? 'Free magazine' : 'Premium magazine' }}" title="{{ $magazine->title }}" />
<section class="inner-section section-shell magazine-detail"><img src="{{ $magazine->cover_image ? asset(ltrim($magazine->cover_image,'/')) : asset('logos and images/Kisaan world-transparent.png') }}" alt="{{ $magazine->cover_image_alt ?: $magazine->title }}"><div><div class="detail-price"><strong>{{ $magazine->is_free ? 'Free' : 'Rs. '.number_format((float)$magazine->price) }}</strong></div><p>{{ $magazine->description }}</p><div class="hero-ctas">@if($magazine->is_free)<a class="button button-primary" href="{{ route('magazines.read',$magazine) }}">Read online</a>@auth @if($magazine->allow_download)<a class="button outline-dark" href="{{ route('magazines.download',$magazine) }}">Download PDF</a>@endif @endauth @else @auth<form action="{{ route('magazines.purchase',$magazine) }}" method="POST">@csrf<input type="hidden" name="payment_method" value="bank_transfer"><button class="button button-primary">Request purchase</button></form>@else<a class="button button-primary" href="{{ route('login') }}">Login to purchase</a>@endauth @endif</div></div></section>
@endsection
