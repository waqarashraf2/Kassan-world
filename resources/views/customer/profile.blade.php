@extends('layouts.app')
@section('title', 'Profile Settings | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Account settings" title="Profile and security" />
<x-customer-shell title="Profile settings">
    <div class="customer-dashboard-grid">
        <form action="{{ route('customer.profile.update') }}" method="POST" class="customer-card customer-form">@csrf @method('PUT')
            <div class="customer-card-head"><div><h2>Personal details</h2><p>Used for checkout and order communication.</p></div></div>
            <x-form-errors />
            <label>Full name<input name="name" value="{{ old('name', $user->name) }}" required></label>
            <label>Email<input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
            <label>Phone<input name="phone" value="{{ old('phone', $user->phone) }}"></label>
            <label class="check-label"><input type="checkbox" name="email_notifications" value="1" @checked($user->email_notifications)> Receive order emails</label>
            <button class="button button-primary">Save profile</button>
        </form>
        <form action="{{ route('customer.password.update') }}" method="POST" class="customer-card customer-form">@csrf @method('PUT')
            <div class="customer-card-head"><div><h2>Change password</h2><p>Use at least eight characters.</p></div></div>
            <label>Current password<input type="password" name="current_password" autocomplete="current-password" required></label>
            <label>New password<input type="password" name="password" autocomplete="new-password" required></label>
            <label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
            <button class="button button-primary">Update password</button>
        </form>
    </div>
</x-customer-shell>
@endsection
