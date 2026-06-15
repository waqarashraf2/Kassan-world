@props(['title', 'eyebrow' => 'Customer account'])
<section class="customer-section section-shell">
    <aside class="customer-sidebar">
        <div class="customer-profile">
            <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></div>
        </div>
        <nav aria-label="Customer account">
            <a class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">Overview</a>
            <a class="{{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">Orders</a>
            <a class="{{ request()->routeIs('customer.addresses.*') ? 'active' : '' }}" href="{{ route('customer.addresses.index') }}">Saved addresses</a>
            <a class="{{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}" href="{{ route('customer.wishlist.index') }}">Wishlist</a>
            <a href="{{ route('cart.index') }}">Shopping cart</a>
            <a class="{{ request()->routeIs('customer.notifications.*') ? 'active' : '' }}" href="{{ route('customer.notifications.index') }}">Notifications @if(auth()->user()->unreadNotifications()->count())<b>{{ auth()->user()->unreadNotifications()->count() }}</b>@endif</a>
            <a class="{{ request()->routeIs('customer.profile*') ? 'active' : '' }}" href="{{ route('customer.profile') }}">Profile & settings</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout securely</button></form>
    </aside>
    <div class="customer-main">
        <header class="customer-heading"><div><span>{{ $eyebrow }}</span><h1>{{ $title }}</h1></div><a href="{{ route('products.index') }}">Continue shopping</a></header>
        {{ $slot }}
    </div>
</section>
