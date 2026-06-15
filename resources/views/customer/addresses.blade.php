@extends('layouts.app')
@section('title', 'Saved Addresses | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Delivery settings" title="Saved addresses" text="Keep checkout fast with trusted delivery details." />
<x-customer-shell title="Saved addresses">
    <div class="address-grid">
        @foreach($addresses as $address)
        <details class="customer-card address-card">
            <summary><div><strong>{{ $address->label }}</strong>@if($address->is_default)<span>Default</span>@endif<small>{{ $address->recipient_name }} · {{ $address->city }}</small></div><b>Edit</b></summary>
            <form action="{{ route('customer.addresses.update', $address) }}" method="POST" class="customer-form">@csrf @method('PUT')
                @include('customer.partials.address-fields', ['address' => $address])
                <button class="button button-primary">Update address</button>
            </form>
            <form action="{{ route('customer.addresses.destroy', $address) }}" method="POST">@csrf @method('DELETE')<button class="text-danger">Remove address</button></form>
        </details>
        @endforeach
        <form action="{{ route('customer.addresses.store') }}" method="POST" class="customer-card customer-form">@csrf
            <div class="customer-card-head"><div><h2>Add address</h2><p>Save a home, farm or office location.</p></div></div>
            @include('customer.partials.address-fields', ['address' => null])
            <button class="button button-primary">Save address</button>
        </form>
    </div>
</x-customer-shell>
@endsection
