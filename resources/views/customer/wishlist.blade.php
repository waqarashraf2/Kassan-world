@extends('layouts.app')
@section('title', 'Wishlist | KISANWORLD')
@section('content')
<x-page-hero eyebrow="Saved products" title="Your wishlist" />
<x-customer-shell title="Wishlist">
    <div class="product-grid customer-products">@forelse($products as $product)<div class="wishlist-item"><x-product-card :product="$product" /><form action="{{ route('customer.wishlist.toggle', $product) }}" method="POST">@csrf<button>Remove from wishlist</button></form></div>@empty<div class="customer-empty">You have not saved any products yet.</div>@endforelse</div>
    <div class="pagination-wrap">{{ $products->links() }}</div>
</x-customer-shell>
@endsection
