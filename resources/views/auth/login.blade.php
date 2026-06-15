@extends('layouts.app')
@section('title', 'Login | KISANWORLD')
@section('meta_description', 'Sign in securely to your KISANWORLD customer account.')
@section('content')
<x-page-hero eyebrow="Your account" title="Welcome back" text="Track orders, save addresses and manage your farming purchases." />
<section class="inner-section section-shell auth-wrap">
    <form action="{{ route('login.store') }}" method="POST" class="site-form auth-card">
        @csrf
        <div><span class="section-kicker">Secure sign in</span><h2>Access your account</h2></div>
        <x-form-errors />
        <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
        <div class="auth-options"><label class="check-label"><input type="checkbox" name="remember" value="1"> Remember me</label><a href="{{ route('password.request') }}">Forgot password?</a></div>
        <button class="button button-primary" type="submit">Login securely</button>
        @if(config('services.google.client_id') && config('services.google.client_secret'))
            <div class="auth-divider"><span>or</span></div>
            <a href="{{ route('google.redirect') }}" class="google-button"><b>G</b> Continue with Google</a>
        @endif
        <p>New here? <a href="{{ route('register') }}">Create an account</a></p>
    </form>
</section>
@endsection
