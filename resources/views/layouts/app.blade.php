<!doctype html>
<html lang="@yield('lang', 'en')" dir="@yield('dir', 'ltr')" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0d6b3b">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KISANWORLD | کسان ورلڈ')</title>
    <meta name="description" content="@yield('meta_description', 'KISANWORLD brings trusted agricultural products, practical farming knowledge, videos and magazines to Pakistan’s farmers.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="KISANWORLD">
    <link rel="icon" type="image/jpeg" sizes="32x32" href="{{ asset('logos and images/Kisaan world.jpeg') }}">
    <meta property="og:title" content="@yield('og_title', 'KISANWORLD | کسان ورلڈ')">
    <meta property="og:description" content="@yield('og_description', 'Products, knowledge and trusted guidance for better farming.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('logos and images/hero-1920.jpg'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'KISANWORLD | کسان ورلڈ')">
    <meta name="twitter:description" content="@yield('og_description', 'Products, knowledge and trusted guidance for better farming.')">
    <meta name="twitter:image" content="@yield('og_image', asset('logos and images/hero-1920.jpg'))">
    <link rel="preload" as="image" href="{{ asset('logos and images/Kisaan world.jpeg') }}" fetchpriority="high">
    <x-frontend-assets />
    @yield('head')
    @stack('head')
</head>
<body class="bg-stone-50 text-slate-900 antialiased">
    <a href="#main-content" class="skip-link">Skip to content</a>
    <x-site-header />
    @if(session('success'))<div class="site-toast success" role="status">{{ session('success') }}</div>@endif
    <main id="main-content">@yield('content')</main>
    <x-site-footer />
    <x-chat-widget />
    @stack('scripts')
</body>
</html>
