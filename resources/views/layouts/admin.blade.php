<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Admin') | KISANWORLD</title>
    <x-frontend-assets />
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar" data-admin-sidebar>
            <a class="admin-logo" href="{{ route('admin.dashboard') }}">KISAN<span>WORLD</span><small>Admin Panel</small></a>
            <nav>
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Products</a>
                <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Product Categories</a>
                <a class="{{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}" href="{{ route('admin.blogs.index') }}">Blogs</a>
                <a class="{{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}" href="{{ route('admin.blog-categories.index') }}">Blog Categories</a>
                <a class="{{ request()->routeIs('admin.videos.*') ? 'active' : '' }}" href="{{ route('admin.videos.index') }}">Videos</a>
                <a class="{{ request()->routeIs('admin.magazines.*') ? 'active' : '' }}" href="{{ route('admin.magazines.index') }}">Magazines</a>
                <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Orders</a>
                <a class="{{ request()->routeIs('admin.magazine-purchases.*') ? 'active' : '' }}" href="{{ route('admin.magazine-purchases.index') }}">Magazine Sales</a>
                <a class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}">Inquiries</a>
                <a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">Settings & SEO</a>
            </nav>
            <div class="admin-sidebar-footer">
                <a href="{{ route('home') }}" target="_blank">View Website</a>
                <form action="{{ route('admin.logout') }}" method="POST">@csrf<button type="submit">Logout</button></form>
            </div>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <button type="button" class="admin-menu-button" data-admin-menu aria-label="Toggle admin menu">☰</button>
                <div><strong>@yield('heading', 'Admin')</strong><span>{{ now()->format('l, F j, Y') }}</span></div>
                <div class="admin-user"><span>{{ auth()->user()->name }}</span><b>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</b></div>
            </header>
            <main class="admin-content">
                @if(session('success'))<div class="admin-alert success">{{ session('success') }}</div>@endif
                @if($errors->any())<div class="admin-alert error"><strong>Please correct these fields:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
