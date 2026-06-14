<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin Login | KISANWORLD</title>
    <x-frontend-assets />
</head>
<body class="admin-login-body">
    <main class="admin-login-card">
        <a href="{{ route('home') }}" class="admin-logo">KISAN<span>WORLD</span><small>Secure Administration</small></a>
        <h1>Welcome back</h1>
        <p>Sign in with your administrator account.</p>
        @if($errors->any())<div class="admin-alert error">{{ $errors->first() }}</div>@endif
        <form action="{{ route('admin.login.store') }}" method="POST" class="admin-form">@csrf
            <label>Email address<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"></label>
            <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
            <label class="admin-check"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
            <button class="admin-primary" type="submit">Sign in</button>
        </form>
    </main>
</body>
</html>
