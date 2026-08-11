@extends('layouts.admin')
@section('title', 'Add Special Offer')
@section('heading', 'Add Special Offer')
@section('content')
<div class="admin-page-heading">
    <div>
        <h1>Create Special Offer</h1>
    </div>
    <a class="admin-secondary" href="{{ route('admin.special-offers.index') }}">Back</a>
</div>
<form action="{{ route('admin.special-offers.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-stack">
    @csrf
    @include('admin.special-offers.form')
    <div class="admin-form-actions">
        <button class="admin-primary">Create Special Offer</button>
    </div>
</form>
@endsection
