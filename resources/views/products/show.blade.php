@extends('layouts.app')
@section('title', ($product->meta_title ?: $product->name).' | KISANWORLD')
@section('meta_description', $product->meta_description ?: ($product->short_description ?: 'Buy '.$product->name.' from KISANWORLD.'))
@section('canonical', $product->canonical_url ?: route('products.show', $product))
@section('og_image', $product->og_image_url ?: asset('logos and images/Kisaan world.jpeg'))
@push('head')
<script type="application/ld+json">{!! json_encode(['@'.'context'=>'https://schema.org','@type'=>'Product','name'=>$product->name,'description'=>$product->short_description ?: strip_tags((string) $product->description),'image'=>$product->images->map(fn($image)=>$image->url)->values(),'sku'=>$product->sku,'offers'=>['@type'=>'Offer','priceCurrency'=>'PKR','price'=>(float)$product->sale_price,'availability'=>$product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock','url'=>route('products.show',$product)]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@section('content')
<x-page-hero eyebrow="{{ $product->category?->name ?? 'Product' }}" title="{{ $product->name }}" />
@php
    $galleryImages = $product->images->map(fn ($image) => [
        'src' => $image->url,
        'alt' => $image->alt_text ?: $product->name,
    ])->values();
    if ($galleryImages->isEmpty()) {
        $galleryImages = collect([['src' => asset('logos and images/Kisaan world.jpeg'), 'alt' => $product->name]]);
    }
@endphp
<section class="inner-section section-shell product-detail">
    <div class="product-gallery" data-product-detail-gallery data-gallery-images='@json($galleryImages)' data-interval="10000">
        <div class="product-main-image">
            <img src="{{ $galleryImages->first()['src'] }}" alt="{{ $galleryImages->first()['alt'] }}" data-gallery-main>
            @if ($galleryImages->count() > 1)
                <button type="button" class="gallery-arrow gallery-arrow-prev" data-gallery-prev aria-label="Show previous product image">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button type="button" class="gallery-arrow gallery-arrow-next" data-gallery-next aria-label="Show next product image">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <span class="gallery-counter" aria-live="polite"><b data-gallery-current>1</b> / {{ $galleryImages->count() }}</span>
            @endif
        </div>
        @if ($galleryImages->count() > 1)<div class="product-thumbs" data-gallery-thumbs>@foreach($galleryImages as $index => $image)<button type="button" class="{{ $index === 0 ? 'is-active' : '' }}" data-gallery-thumb="{{ $image['src'] }}" data-gallery-alt="{{ $image['alt'] }}" data-gallery-index="{{ $index }}"><img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}" loading="lazy"></button>@endforeach</div>@endif
    </div>
    <div class="product-info">
        <span class="stock-line {{ $product->in_stock ? 'available' : '' }}">{{ $product->in_stock ? 'In stock' : 'Out of stock' }}</span>
        <h2>{{ $product->name }}</h2>
        @if($product->name_ur)<p class="detail-urdu" lang="ur" dir="rtl">{{ $product->name_ur }}</p>@endif
        <div class="detail-price"><strong>Rs. {{ number_format((float)$product->sale_price) }}</strong>@if($product->discount_price)<del>Rs. {{ number_format((float)$product->price) }}</del>@endif</div>
        <p>{{ $product->short_description }}</p>
        <form action="{{ route('cart.store', $product) }}" method="POST" class="buy-form" data-ajax-cart>@csrf<label>Quantity<input type="number" name="quantity" min="1" max="100" value="1"></label><button type="submit" class="button button-primary" @disabled(!$product->in_stock)><span class="button-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2 10h10l2-7H7M10 19a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm8 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/></svg></span><span data-button-label>Add to Cart</span></button><a href="tel:+92{{ ltrim($siteSettings['site_phone'] ?? '03226780242', '0') }}" class="button outline-button">Call to order</a></form>
        @auth<form action="{{ route('customer.wishlist.toggle', $product) }}" method="POST" class="detail-wishlist">@csrf<button type="submit">Save this product to wishlist</button></form>@endauth
    </div>
</section>
@if($product->description || $product->description_ur)
<section class="product-description-section section-shell">
    <div class="description-heading"><span class="section-kicker">Product guide</span><h2>Details, benefits and application</h2></div>
    <div class="description-columns {{ $product->description_ur ? 'has-urdu' : '' }}">
        @if($product->description)<div class="product-description"><h3>Product description</h3><div class="prose">{!! $product->description !!}</div></div>@endif
        @if($product->description_ur)<div class="product-description urdu-description" lang="ur" dir="rtl"><h3>مصنوعات کی تفصیل</h3><div class="prose">{!! $product->description_ur !!}</div></div>@endif
    </div>
</section>
@endif
@if($relatedProducts->isNotEmpty())<section class="inner-section section-shell"><div class="section-heading"><div><span class="section-kicker">You may also like</span><h2>Related products</h2></div></div><div class="product-grid">@foreach($relatedProducts as $related)<x-product-card :product="$related" />@endforeach</div></section>@endif
@endsection
