@extends('layouts.admin')
@section('title', 'Edit Magazine')
@section('heading', 'Edit Magazine')
@section('content')
<div class="admin-page-heading"><div><h1>{{ $magazine->title }}</h1></div><a class="admin-secondary" href="{{ route('admin.magazines.index') }}">Back</a></div><form action="{{ route('admin.magazines.update',$magazine) }}" method="POST" enctype="multipart/form-data" class="admin-form-stack">@csrf @method('PUT') @include('admin.magazines.form')<div class="admin-form-actions"><button class="admin-primary">Save Changes</button></div></form>
@endsection
