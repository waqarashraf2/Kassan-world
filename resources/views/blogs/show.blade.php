@extends('layouts.app')

@section('lang', $language === 'ur' ? 'ur' : 'en')
@section('dir', $language === 'ur' ? 'rtl' : 'ltr')
@section('title', ($blog->meta_title ?: $blog->title).' | KISANWORLD')
@section('meta_description', $blog->meta_description ?: $blog->excerpt)
@section('canonical', $blog->canonical_url ?: url()->current())
@section('og_type', 'article')
@section('og_image', $blog->featured_image_url ?: asset('logos and images/hero-1920.jpg'))

@push('head')
    <script type="application/ld+json">{!! json_encode([
        '@'.'context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $blog->title,
        'datePublished' => $blog->published_at?->toAtomString(),
        'dateModified' => $blog->updated_at->toAtomString(),
        'author' => [
            '@type' => 'Person',
            'name' => $blog->author?->name ?? 'KISANWORLD',
        ],
        'image' => $blog->featured_image_url ?: asset('logos and images/hero-1920.jpg'),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <x-page-hero
        :eyebrow="$blog->category?->name ?? 'Agriculture'"
        :title="$blog->title"
        :text="$blog->excerpt"
    />

    <article class="article-detail section-shell">
        @if($blog->featured_image_url)
            <img
                class="article-cover"
                src="{{ $blog->featured_image_url }}"
                alt="{{ $blog->featured_image_alt ?: $blog->title }}"
            >
        @endif
        <div class="article-meta">
            By {{ $blog->author?->name ?? 'KISANWORLD' }} · {{ $blog->published_at?->format('F d, Y') }}
        </div>
        <div class="prose">{!! $blog->content !!}</div>
    </article>

    @if($relatedBlogs->isNotEmpty())
        <section class="inner-section section-shell">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">Keep reading</span>
                    <h2>Related articles</h2>
                </div>
            </div>
            <div class="article-grid">
                @foreach($relatedBlogs->take(3) as $related)
                    <x-blog-card :blog="$related" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
