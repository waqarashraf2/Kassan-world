@extends('layouts.admin')
@section('title', 'Add Magazine')
@section('heading', 'Add Magazine')
@section('content')
<div class="admin-page-heading"><div><h1>Create magazine</h1></div><a class="admin-secondary" href="{{ route('admin.magazines.index') }}">Back</a></div><form action="{{ route('admin.magazines.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-stack">@csrf @include('admin.magazines.form')<div class="admin-form-actions"><button class="admin-primary">Create Magazine</button></div></form>
@endsection
