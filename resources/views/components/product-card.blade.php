@props(['product'])
@php
    $images = $product->images->map(fn ($image) => $image->url)->values();
    $fallback = asset('logos and images/Kisaan world-transparent.png');
    $primaryImage = $images->first() ?? $fallback;
    $discount = $product->discount_price && (float) $product->price > 0
        ? (int) round((1 - ((float) $product->discount_price / (float) $product->price)) * 100)
        : null;
@endphp
<article class="product-card reveal-card" data-product-card data-images='@json($images->isNotEmpty() ? $images : [$fallback])' data-interval="10000">
    <div class="product-media">
        @auth
            <form action="{{ route('customer.wishlist.toggle', $product) }}" method="POST" class="wishlist-toggle">@csrf<button type="submit" aria-label="Save {{ $product->name }} to wishlist"><svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></button></form>
        @endauth
        <a href="{{ route('products.show', $product) }}" class="product-image-link" aria-label="View {{ $product->name }}">
            <img src="{{ $primaryImage }}" alt="{{ $product->images->first()?->alt_text ?: $product->name }}" width="600" height="600" loading="lazy" decoding="async" data-product-image>
        </a>
        <span class="stock-badge {{ $product->in_stock ? 'in-stock' : 'out-stock' }}">{{ $product->in_stock ? 'In Stock' : 'Out of Stock' }}</span>
        @if ($discount)<span class="discount-badge">-{{ $discount }}%</span>@endif
        @if ($images->count() > 1)
            <button type="button" class="card-carousel-arrow card-carousel-prev" data-card-prev aria-label="Previous image of {{ $product->name }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button type="button" class="card-carousel-arrow card-carousel-next" data-card-next aria-label="Next image of {{ $product->name }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </button>
            <span class="card-carousel-counter"><b data-card-current>1</b>/{{ $images->count() }}</span>
        @endif
    </div>
    <div class="product-body">
        <a class="product-category" href="{{ route('products.index', ['category' => $product->category?->slug]) }}">{{ $product->category?->name ?? 'Agriculture' }}</a>
        <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
        @if ($product->name_ur)<p class="product-name-ur" lang="ur" dir="rtl">{{ $product->name_ur }}</p>@endif
        <div class="price-row"><strong>Rs. {{ number_format((float) $product->sale_price) }}</strong>@if ($product->discount_price)<del>Rs. {{ number_format((float) $product->price) }}</del>@endif</div>
        <div class="product-actions">
            <form action="{{ route('cart.store', $product) }}" method="POST" data-ajax-cart>@csrf<input type="hidden" name="quantity" value="1">
                <button type="submit" class="add-cart" @disabled(! $product->in_stock)><span class="button-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2 10h10l2-7H7M10 19a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm8 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/></svg></span><span data-button-label>Add to Cart</span></button>
            </form>
            <a href="{{ route('products.show', $product) }}" class="view-product" aria-label="View {{ $product->name }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-5-5 5 5-5 5"/></svg></a>
        </div>
    </div>
</article>
