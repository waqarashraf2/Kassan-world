<header class="site-header" data-site-header>
    <div class="header-shell">
        <a href="{{ route('home') }}" class="brand" aria-label="KISANWORLD home">
            <span class="brand-mark"><img src="{{ asset('logos and images/Kisaan world.jpeg') }}" alt="KISANWORLD کسان ورلڈ logo" width="679" height="504"></span>
            <span class="brand-copy"><strong>KISAN<span>WORLD</span></strong><small lang="ur" dir="rtl">کسان ورلڈ</small></span>
        </a>
        <nav class="desktop-nav" aria-label="Main navigation">
            <a href="{{ route('products.index') }}">Products</a>
            <a href="{{ route('blogs.urdu.index') }}">Blogs Urdu</a>
            <a href="{{ route('blogs.english.index') }}">Blogs English</a>
            <a href="{{ route('videos.index') }}">Videos</a>
            <a href="{{ route('magazines.index') }}">Magazine</a>
            <a href="{{ route('about') }}">About Us</a>
            <a href="{{ route('contact.create') }}">Contact Us</a>
        </nav>
        <div class="header-actions">
            @auth
                <a href="{{ route('customer.dashboard') }}" class="account-button" aria-label="Open customer dashboard"><span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><b>Account</b></a>
            @else
                <a href="{{ route('login') }}" class="account-button"><span aria-hidden="true">&#128100;</span><b>Login</b></a>
            @endauth
            <a href="{{ route('cart.index') }}" class="icon-button" aria-label="Open shopping cart">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 8H7M10 20a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm8 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/></svg>
                <span class="cart-count">{{ collect(session('cart', []))->sum() }}</span>
            </a>
            <a href="tel:+92{{ ltrim($siteSettings['site_phone'] ?? '03226780242', '0') }}" class="call-button">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8.5 3.5 11 8 9.2 9.8a15 15 0 0 0 5 5L16 13l4.5 2.5-1.2 4a2 2 0 0 1-2.2 1.4C9.4 19.8 4.2 14.6 3.1 6.9A2 2 0 0 1 4.5 4.7l4-1.2Z"/></svg>
                <span>Call Now</span>
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
                <span></span><span></span><span></span><span class="sr-only">Toggle menu</span>
            </button>
        </div>
    </div>
    <nav id="mobile-menu" class="mobile-menu" aria-label="Mobile navigation" data-mobile-menu hidden>
        <a href="{{ route('products.index') }}">Products</a>
        <a href="{{ route('blogs.urdu.index') }}">Blogs Urdu</a>
        <a href="{{ route('blogs.english.index') }}">Blogs English</a>
        <a href="{{ route('videos.index') }}">Videos</a>
        <a href="{{ route('magazines.index') }}">Magazine</a>
        <a href="{{ route('about') }}">About Us</a>
        <a href="{{ route('contact.create') }}">Contact Us</a>
        @auth<a href="{{ route('customer.dashboard') }}">My Account</a>@else<a href="{{ route('login') }}">Login / Register</a>@endauth
        <a href="tel:+92{{ ltrim($siteSettings['site_phone'] ?? '03226780242', '0') }}" class="mobile-call">Call {{ $siteSettings['site_phone'] ?? '03226780242' }}</a>
    </nav>
    <div class="header-strip">
        <span>Trusted agricultural products</span><span>•</span>
        <span lang="ur" dir="rtl">پاکستان کے کسانوں کے لیے</span><span>•</span>
        <span>Nationwide support</span>
    </div>
</header>
