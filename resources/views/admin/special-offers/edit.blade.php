@extends('layouts.admin')
@section('title', 'Edit Special Offer')
@section('heading', 'Edit Special Offer')
@section('content')
<div class="admin-page-heading">
    <div>
        <h1>Edit: {{ $specialOffer->name }}</h1>
    </div>
    <a class="admin-secondary" href="{{ route('admin.special-offers.index') }}">Back</a>
</div>
<form action="{{ route('admin.special-offers.update', $specialOffer) }}" method="POST" enctype="multipart/form-data" class="admin-form-stack">
    @csrf
    @method('PUT')
    @include('admin.special-offers.form')
    <div class="admin-form-actions">
        <button class="admin-primary">Save Changes</button>
    </div>
</form>
@endsection
