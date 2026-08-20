@extends('layouts.admin')
@section('title', 'Special Offers')
@section('heading', 'Special Offers')
@section('content')
<div class="admin-page-heading">
    <div>
        <h1>Special Offers</h1>
        <p>Manage seasonal and festival promotions (e.g. Azadi Offer, Eid Offer) to highlight products.</p>
    </div>
    <a class="admin-primary" href="{{ route('admin.special-offers.create') }}">Add Special Offer</a>
</div>
<section class="admin-card">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Banner</th>
                    <th>Name</th>
                    <th>Discount</th>
                    <th>Products</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($specialOffers as $offer)
                    <tr>
                        <td>
                            @if($offer->banner_image)
                                <img src="{{ $offer->banner_image_url }}" alt="{{ $offer->name }}" style="height: 40px; border-radius: 4px; object-fit: cover;">
                            @else
                                <span class="admin-status info">No banner</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $offer->name }}</strong>
                            <small>{{ $offer->name_ur }}</small>
                        </td>
                        <td>
                            @if($offer->discount_percentage)
                                <span class="admin-status completed">{{ $offer->discount_percentage }}% Off</span>
                            @else
                                <span class="admin-status info">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="admin-status {{ $offer->products_count > 0 ? 'completed' : 'info' }}">
                                {{ $offer->products_count }} {{ Str::plural('Product', $offer->products_count) }}
                            </span>
                        </td>
                        <td>{{ $offer->start_date ? $offer->start_date->format('Y-m-d') : 'No limit' }}</td>
                        <td>{{ $offer->end_date ? $offer->end_date->format('Y-m-d') : 'No limit' }}</td>
                        <td>
                            <span class="admin-status {{ $offer->is_active ? 'completed' : 'cancelled' }}">
                                {{ $offer->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="admin-actions">
                            <a href="{{ route('admin.special-offers.edit', $offer) }}">Edit</a>
                            <x-admin.delete-form :action="route('admin.special-offers.destroy', $offer)" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="admin-empty">No special offers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-pagination">
        {{ $specialOffers->links() }}
    </div>
</section>
@endsection
