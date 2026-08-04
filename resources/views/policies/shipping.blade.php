@extends('layouts.app')

@section('title', 'Shipping and Service Policy | KISANWORLD')
@section('meta_description', 'KISANWORLD shipping and service policy for product delivery, magazine access and support in Pakistan.')
@section('canonical', route('policies.shipping'))

@section('content')
<x-page-hero eyebrow="Policy" title="Shipping and Service Policy" text="Delivery, service access and support details for KISANWORLD customers." />
<section class="inner-section section-shell policy-page">
    <p class="policy-updated">Last updated: August 5, 2026</p>
    <div class="policy-content">
        <h2>Delivery Coverage</h2>
        <p>KISANWORLD serves customers across Pakistan where courier or goods transport service is available. Delivery may vary by product type, quantity, destination city and courier coverage.</p>

        <h2>Delivery Time</h2>
        <p>Most orders are processed after phone confirmation and payment verification where applicable. Estimated delivery is usually 5 to 7 working days, but remote areas, weather, holidays or transport delays may require more time.</p>

        <h2>Shipping Charges</h2>
        <p>Shipping charges may depend on product weight, packaging, destination and delivery method. Any applicable delivery charges are confirmed before dispatch or shown during checkout where configured.</p>

        <h2>Digital Magazine Access</h2>
        <p>Free magazines are available immediately where enabled. Paid magazine access is provided after payment verification by the KISANWORLD admin team.</p>

        <h2>Failed Delivery</h2>
        <p>If a delivery fails because the customer is unreachable, address details are incomplete or the parcel is refused, re-delivery charges may apply.</p>

        <h2>Support</h2>
        <p>For delivery or service help, call or WhatsApp {{ $siteSettings['site_phone'] ?? '03226780242' }}.</p>
        <p><strong>Office:</strong> {{ $siteSettings['site_address'] ?? 'KISANWORLD Marketing, Lahore, Pakistan' }}</p>
    </div>
</section>
@endsection
