@extends('layouts.app')
@section('title', 'Your Cart | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Your order" title="Shopping cart" text="Review quantities before moving to checkout." />
<section class="inner-section section-shell">
    @if($products->isEmpty())<div class="empty-state"><strong>Your cart is empty.</strong><a href="{{ route('products.index') }}">Explore products →</a></div>
    @else
        <div class="cart-layout"><div class="cart-lines">
            @foreach($products as $product)
                @php
                    $cartImages = $product->images->map(fn ($image) => $image->url)->values();
                    $cartFallback = asset('logos and images/Kisaan world-transparent.png');
                @endphp
                <article class="cart-line" data-rotating-media data-images='@json($cartImages->isNotEmpty() ? $cartImages : [$cartFallback])' data-interval="10000">
                    <a href="{{ route('products.show',$product) }}" class="cart-line-media"><img src="{{ $cartImages->first() ?? $cartFallback }}" alt="{{ $product->name }}" data-rotating-image></a>
                    <div><h2><a href="{{ route('products.show',$product) }}">{{ $product->name }}</a></h2><strong>Rs. {{ number_format((float)$product->sale_price) }}</strong></div><form action="{{ route('cart.update',$product) }}" method="POST">@csrf @method('PATCH')<input type="number" name="quantity" value="{{ $cart[$product->id] }}" min="1" max="100"><button>Update</button></form><form action="{{ route('cart.destroy',$product) }}" method="POST">@csrf @method('DELETE')<button class="remove-button">Remove</button></form>
                </article>
            @endforeach
        </div><aside class="cart-summary"><span>Order total</span><strong>Rs. {{ number_format($products->sum(fn($product)=>(float)$product->sale_price * $cart[$product->id])) }}</strong><a class="button button-primary" href="{{ route('checkout.create') }}">Proceed to checkout</a></aside></div>
    @endif
</section>
@endsection
