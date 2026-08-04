@extends('layouts.app')

@section('title', 'Privacy Policy | KISANWORLD')
@section('meta_description', 'KISANWORLD privacy policy for customer data, orders, payments, support messages and account information.')
@section('canonical', route('policies.privacy'))

@section('content')
<x-page-hero eyebrow="Policy" title="Privacy Policy" text="How KISANWORLD collects, uses and protects customer information." />
<section class="inner-section section-shell policy-page">
    <p class="policy-updated">Last updated: August 5, 2026</p>
    <div class="policy-content">
        <h2>Information We Collect</h2>
        <p>We collect information needed to process orders, magazine purchases, support requests and customer accounts. This may include name, phone number, email address, delivery address, city, order notes, payment method, payment reference and uploaded bank transfer proof.</p>

        <h2>How We Use Information</h2>
        <p>Customer information is used for order confirmation, delivery coordination, payment verification, magazine access, customer support, fraud prevention and service improvement.</p>

        <h2>Payments</h2>
        <p>KISANWORLD does not ask customers to enter card number, CVV, PIN or OTP on our website forms. Online card details must be entered only on the approved secure payment gateway page. Bank transfer proof is stored privately for admin verification.</p>

        <h2>Sharing Information</h2>
        <p>We may share limited information with delivery partners, payment processors, hosting providers and support tools only where needed to complete the customer request. We do not sell customer personal information.</p>

        <h2>Data Security</h2>
        <p>We use reasonable technical and administrative controls to protect customer data. Customers should avoid sending sensitive payment credentials through chat, email or WhatsApp.</p>

        <h2>Contact</h2>
        <p>For privacy questions, contact {{ $siteSettings['site_phone'] ?? '03226780242' }} or {{ $siteSettings['site_email'] ?? 'kisanworld.magazine@gmail.com' }}.</p>
        <p><strong>Office:</strong> {{ $siteSettings['site_address'] ?? 'KISANWORLD Marketing, Lahore, Pakistan' }}</p>
    </div>
</section>
@endsection
