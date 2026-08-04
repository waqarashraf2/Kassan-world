@extends('layouts.app')

@section('title', 'Refund and Return Policy | KISANWORLD')
@section('meta_description', 'KISANWORLD refund, return and cancellation policy for physical products and magazine purchases.')
@section('canonical', route('policies.refund'))

@section('content')
<x-page-hero eyebrow="Policy" title="Refund and Return Policy" text="How returns, refunds and cancellations are handled." />
<section class="inner-section section-shell policy-page">
    <p class="policy-updated">Last updated: August 5, 2026</p>
    <div class="policy-content">
        <h2>Order Cancellation</h2>
        <p>Customers may request cancellation before the order is dispatched. Once an order has been shipped, cancellation depends on courier status and product condition.</p>

        <h2>Returns</h2>
        <p>Return requests should be made within 7 days of delivery. Products must be unused, sealed where applicable, and returned with original packaging. Agricultural inputs, opened packs, used items and perishable or temperature-sensitive items may not be returnable unless they are damaged, incorrect or defective on arrival.</p>

        <h2>Damaged or Wrong Items</h2>
        <p>If you receive a damaged, leaked, expired, defective or incorrect item, contact us as soon as possible with your order number, photos and delivery details. We will review the case and offer replacement, exchange or refund where valid.</p>

        <h2>Refund Processing</h2>
        <p>Approved refunds are processed to the original payment method or another agreed local method after verification. Bank and wallet processing time may vary.</p>

        <h2>Magazine Purchases</h2>
        <p>Paid digital magazine purchases are generally non-refundable after access is unlocked. If payment was made but access was not provided due to a technical or verification issue, contact support for resolution.</p>

        <h2>Contact</h2>
        <p>For refund, return or cancellation help, contact {{ $siteSettings['site_phone'] ?? '03226780242' }} or {{ $siteSettings['site_email'] ?? 'kisanworld.magazine@gmail.com' }}.</p>
        <p><strong>Office:</strong> {{ $siteSettings['site_address'] ?? 'KISANWORLD Marketing, Lahore, Pakistan' }}</p>
    </div>
</section>
@endsection
