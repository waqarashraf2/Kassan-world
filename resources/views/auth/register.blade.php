@extends('layouts.app')
@section('title', 'Create Account | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Join KISANWORLD" title="Create your account" text="A faster way to order, save delivery details and track every purchase." />
<section class="inner-section section-shell auth-wrap">
    <form action="{{ route('register.store') }}" method="POST" class="site-form auth-card">
        @csrf
        <x-form-errors />
        <label>Full name<input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
        <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <label>Phone number<input name="phone" value="{{ old('phone') }}" autocomplete="tel"></label>
        <label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label>
        <label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button class="button button-primary" type="submit">Create account</button>
        @if(config('services.google.client_id') && config('services.google.client_secret'))
            <div class="auth-divider"><span>or</span></div>
            <a href="{{ route('google.redirect') }}" class="google-button"><b>G</b> Continue with Google</a>
        @endif
        <p>Already registered? <a href="{{ route('login') }}">Login</a></p>
    </form>
</section>
@endsection
