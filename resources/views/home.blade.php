@extends('layouts.app')

@section('title', 'KISANWORLD | Agricultural Products, Knowledge & Magazines')
@section('meta_description', 'Shop trusted fertilizers, seeds, pesticides and farming tools. Read practical Urdu and English agriculture guides, watch videos and explore KISANWORLD magazines.')
@section('canonical', route('home'))

@push('head')
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'KISANWORLD', 'alternateName' => 'کسان ورلڈ', 'url' => route('home'), 'logo' => asset('logos and images/Kisaan world-transparent.png'), 'sameAs' => array_values(array_filter([$siteSettings['whatsapp_url'] ?? null, $siteSettings['youtube_url'] ?? null, $siteSettings['facebook_url'] ?? null, $siteSettings['instagram_url'] ?? null])), 'contactPoint' => ['@type' => 'ContactPoint', 'telephone' => '+92'.ltrim($siteSettings['site_phone'] ?? '03226780242', '0'), 'contactType' => 'customer service', 'areaServed' => 'PK']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<section class="hero" aria-labelledby="hero-title">
    <div class="hero-background" aria-hidden="true"></div><div class="hero-pattern" aria-hidden="true"></div>
    <div class="hero-content">
        <div class="hero-copy">
            <div class="eyebrow hero-animate"><span></span> Pakistan's agriculture knowledge & commerce platform</div>
            <h1 id="hero-title" class="hero-animate">Grow smarter.<br><em>Harvest stronger.</em></h1>
            <p class="hero-lead hero-animate">Trusted farm inputs, practical crop guidance and a connected community, built to help Pakistan's farmers move forward.</p>
            <p class="hero-urdu hero-animate" lang="ur" dir="rtl">بہتر پیداوار، جدید رہنمائی، خوشحال کسان</p>
            <div class="hero-ctas hero-animate">
                <a href="#featured-products" class="button button-primary">Shop Products <span>→</span></a>
                <a href="{{ route('blogs.urdu.index') }}" class="button button-glass">Read Blogs</a>
                <a href="{{ route('contact.create') }}" class="button button-text">Contact Us <span>↗</span></a>
            </div>
            <dl class="hero-stats hero-animate">
                <div><dt>Quality</dt><dd>Verified products</dd></div><div><dt>Bilingual</dt><dd>Urdu & English</dd></div><div><dt>Support</dt><dd>Farmer-first service</dd></div>
            </dl>
        </div>
        <aside class="hero-panel hero-animate" aria-label="KISANWORLD services">
            <div class="panel-heading"><span>Everything for a better season</span><small>Explore our ecosystem</small></div>
            <a href="{{ route('products.index') }}" class="service-row"><span class="service-icon">01</span><span><strong>Crop Nutrition</strong><small>Fertilizers & growth solutions</small></span><b>↗</b></a>
            <a href="{{ route('products.index') }}" class="service-row"><span class="service-icon">02</span><span><strong>Seeds & Protection</strong><small>Trusted inputs for healthy crops</small></span><b>↗</b></a>
            <a href="{{ route('blogs.urdu.index') }}" class="service-row"><span class="service-icon">03</span><span><strong>Farmer Knowledge</strong><small>Field-ready guides in Urdu</small></span><b>↗</b></a>
            <a href="{{ route('magazines.index') }}" class="service-row"><span class="service-icon">04</span><span><strong>Kisan World Magazine</strong><small>Seasonal insight, online & offline</small></span><b>↗</b></a>
        </aside>
    </div>
    <a href="#featured-products" class="scroll-cue" aria-label="Scroll to products"><span>Scroll to explore</span><i></i></a>
</section>

@if ($specialOffers->isNotEmpty())
<section class="special-offers-section section-shell reveal" style="padding-top: 4rem; padding-bottom: 2rem;">
    @foreach($specialOffers as $offer)
        @if($offer->products->isNotEmpty())
            <div class="special-offer-block" style="margin-bottom: 4rem;">
                <div class="section-heading">
                    <div>
                        <span class="section-kicker" style="color: #e67e22; font-weight: 600;">Seasnol Special Offer</span>
                        <h2 style="margin-top: 0.5rem;">{{ $offer->name }} @if($offer->discount_percentage)<em style="color: #e67e22; font-style: normal;">({{ $offer->discount_percentage }}% Off)</em>@endif</h2>
                        @if($offer->name_ur)
                            <p class="urdu-subtitle" lang="ur" dir="rtl" style="font-size: 1.6rem; color: #27ae60; margin-top: 0.5rem; font-weight: bold;">{{ $offer->name_ur }}</p>
                        @endif
                    </div>
                    @if($offer->description || $offer->description_ur)
                        <div class="section-heading-side">
                            @if($offer->description)
                                <div style="font-size: 1.1rem; line-height: 1.6; color: var(--color-text-light);">{!! $offer->description !!}</div>
                            @endif
                            @if($offer->description_ur)
                                <div lang="ur" dir="rtl" style="font-size: 1.2rem; line-height: 1.6; color: var(--color-text-light); margin-top: 0.5rem; font-weight: bold;">{!! $offer->description_ur !!}</div>
                            @endif
                        </div>
                    @endif
                </div>
                
                @if($offer->banner_image)
                    <div class="special-offer-banner" style="margin-bottom: 2rem; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <img src="{{ $offer->banner_image_url }}" alt="{{ $offer->name }}" style="width: 100%; height: auto; max-height: 350px; object-fit: cover; display: block;">
                    </div>
                @endif

                <div class="product-grid" style="margin-top: 2rem;">
                    @foreach($offer->products->take(4) as $offerProduct)
                        <x-product-card :product="$offerProduct" />
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</section>
@endif

@if ($topProducts->isNotEmpty())
<section id="top-products" class="products-section section-shell reveal" style="background: rgba(39, 174, 96, 0.05); padding-top: 4rem; padding-bottom: 4rem; border-radius: 16px; margin-top: 2rem; margin-bottom: 2rem;">
    <div class="section-heading">
        <div>
            <span class="section-kicker" style="color: #27ae60; font-weight: 600;">Highly Recommended</span>
            <h2>Top <em>Products</em></h2>
            <p class="urdu-title" lang="ur" dir="rtl" style="font-size: 1.6rem; color: #27ae60; margin-top: 0.5rem; font-weight: bold;">بہترین اور اہم مصنوعات</p>
        </div>
        <div class="section-heading-side">
            <p>Farmers' most-trusted fertilizers, seeds, and crop protection products hand-picked for quality performance.</p>
        </div>
    </div>
    <div class="product-grid" style="margin-top: 2rem;">
        @foreach($topProducts as $topProduct)
            <x-product-card :product="$topProduct" />
        @endforeach
    </div>
</section>
@endif

<section id="featured-products" class="products-section section-shell" aria-labelledby="products-title">
    <div class="section-heading reveal">
        <div><span class="section-kicker">Farm essentials</span><h2 id="products-title">Products for every <em>growing season</em></h2></div>
        <div class="section-heading-side"><p>Carefully selected agricultural products with clear pricing, stock information and direct support.</p><a href="{{ route('products.index') }}">View all products <span>→</span></a></div>
    </div>
    <div class="product-grid" data-product-grid><x-product-batch :products="$products" /></div>
    @if ($products->isEmpty())
        <div class="empty-state"><strong>Products are being prepared.</strong><span>Add products from the admin panel and they will appear here automatically in batches of five.</span></div>
    @endif
    @if ($products->nextPageUrl())
        <div class="load-sentinel" data-product-loader data-next-url="{{ route('home.products', ['page' => 2]) }}"><span class="loader-ring" aria-hidden="true"></span><span data-loader-text>Loading more products…</span></div>
    @else
        <div class="product-end">You have reached the end of our current collection.</div>
    @endif
</section>

<section class="trust-band">
    <div class="section-shell trust-grid">
        <div class="trust-intro reveal"><span class="section-kicker light">Why KISANWORLD</span><h2>Rooted in trust.<br>Built for progress.</h2></div>
        <div class="trust-item reveal"><strong>01</strong><h3>Authentic Products</h3><p>Clear sourcing and practical product information for confident decisions.</p></div>
        <div class="trust-item reveal"><strong>02</strong><h3>Local Knowledge</h3><p>Actionable Urdu and English guidance shaped around local farming needs.</p></div>
        <div class="trust-item reveal"><strong>03</strong><h3>Human Support</h3><p>Easy phone and contact support when a farmer needs a real answer.</p></div>
    </div>
</section>

@if ($latestBlogs->isNotEmpty())
<section class="content-section section-shell" aria-labelledby="knowledge-title">
    <div class="section-heading reveal"><div><span class="section-kicker">Field knowledge</span><h2 id="knowledge-title">Ideas that help farms <em>move forward</em></h2></div><div class="section-heading-side"><a href="{{ route('blogs.urdu.index') }}">Explore all articles <span>→</span></a></div></div>
    <div class="article-grid">
        @foreach ($latestBlogs->take(3) as $blog)
            <article class="article-card reveal">
                <a href="{{ route($blog->language === 'ur' ? 'blogs.urdu.show' : 'blogs.english.show', $blog->slug) }}" class="article-image">
                    @if ($blog->featured_image_url)<img src="{{ $blog->featured_image_url }}" alt="{{ $blog->featured_image_alt ?: $blog->title }}" loading="lazy" width="700" height="440">@else<span>{{ $blog->language === 'ur' ? 'کسان ورلڈ' : 'KISANWORLD' }}</span>@endif
                </a>
                <div><span>{{ $blog->category?->name ?? 'Agriculture' }}</span><time datetime="{{ $blog->published_at?->toDateString() }}">{{ $blog->published_at?->format('M d, Y') }}</time></div>
                <h3><a href="{{ route($blog->language === 'ur' ? 'blogs.urdu.show' : 'blogs.english.show', $blog->slug) }}">{{ $blog->title }}</a></h3>
            </article>
        @endforeach
    </div>
</section>
@endif

<section class="cta-section section-shell reveal">
    <div><span class="section-kicker light">Need farming guidance?</span><h2>Let's grow the next season <em>together.</em></h2><p>Talk to KISANWORLD about products, crop needs, orders or magazine access.</p></div>
    <div><a href="{{ route('contact.create') }}" class="button button-orange">Contact our team <span>→</span></a><a href="tel:+92{{ ltrim($siteSettings['site_phone'] ?? '03226780242', '0') }}" class="cta-phone">Call {{ $siteSettings['site_phone'] ?? '03226780242' }}</a></div>
</section>
@endsection
