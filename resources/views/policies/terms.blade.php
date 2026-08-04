@extends('layouts.app')

@section('title', 'Terms and Conditions | KISANWORLD')
@section('meta_description', 'KISANWORLD website terms and conditions for products, magazines, orders, payments and customer use.')
@section('canonical', route('policies.terms'))

@section('content')
<x-page-hero eyebrow="Policy" title="Terms and Conditions" text="Rules for using KISANWORLD products, magazine and payment services." />
<section class="inner-section section-shell policy-page">
    <p class="policy-updated">Last updated: August 5, 2026</p>
    <div class="policy-content">
        <h2>Website Use</h2>
        <p>By using this website or placing an order, you agree to provide accurate contact, delivery and payment information. KISANWORLD may contact you to confirm product availability, pricing, delivery details and payment verification.</p>

        <h2>Products and Pricing</h2>
        <p>Product descriptions, images, stock status and prices are provided for customer guidance. Prices are shown in Pakistani Rupees and may be updated without prior notice. Final availability is confirmed before dispatch.</p>

        <h2>Orders and Payments</h2>
        <p>Orders may be paid through available methods shown at checkout, including cash on delivery where offered, bank transfer, mobile wallet transfer or approved online payment gateway. Bank transfer orders require a valid payment screenshot or receipt for verification.</p>

        <h2>Magazine Purchases</h2>
        <p>Paid magazine access is unlocked after payment verification. Download access, where available, is for the purchasing customer and must not be redistributed without permission.</p>

        <h2>Customer Responsibilities</h2>
        <p>Customers are responsible for reviewing product details, following usage instructions, providing a reachable phone number and receiving orders at the provided address.</p>

        <h2>Contact</h2>
        <p><strong>Office:</strong> {{ $siteSettings['site_address'] ?? 'KISANWORLD Marketing, Lahore, Pakistan' }}</p>
        <p><strong>Phone:</strong> {{ $siteSettings['site_phone'] ?? '03226780242' }}<br><strong>Email:</strong> {{ $siteSettings['site_email'] ?? 'kisanworld.magazine@gmail.com' }}</p>
    </div>
</section>
@endsection
