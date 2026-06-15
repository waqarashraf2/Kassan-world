@extends('layouts.app')
@section('title', 'Forgot Password | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Account recovery" title="Reset your password" text="We will email you a secure, time-limited reset link." />
<section class="inner-section section-shell auth-wrap">
    <form action="{{ route('password.email') }}" method="POST" class="site-form auth-card">
        @csrf
        <x-form-errors />
        <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
        <button class="button button-primary" type="submit">Email reset link</button>
        <p><a href="{{ route('login') }}">Back to login</a></p>
    </form>
</section>
@endsection
