@extends('layouts.app')
@section('title', 'Choose New Password | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Secure recovery" title="Choose a new password" />
<section class="inner-section section-shell auth-wrap">
    <form action="{{ route('password.update') }}" method="POST" class="site-form auth-card">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-form-errors />
        <label>Email address<input type="email" name="email" value="{{ old('email', $email) }}" required></label>
        <label>New password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label>
        <label>Confirm new password<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button class="button button-primary" type="submit">Reset password</button>
    </form>
</section>
@endsection
