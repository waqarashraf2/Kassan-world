@extends('layouts.app')

@section('title', 'About KISANWORLD | Agriculture Platform Pakistan')
@section('meta_description', 'Learn how KISANWORLD Marketing Lahore connects Pakistan’s farmers with agricultural products, practical knowledge, videos and magazines.')
@section('canonical', route('about'))

@section('content')
<x-page-hero eyebrow="About KISANWORLD" :title="$siteSettings['about_title'] ?? 'Products, knowledge and support for better farming.'" text="An agriculture-focused commerce and learning platform serving farmers across Pakistan." />

<section class="about-section section-shell">
    <div class="about-copy reveal">
        <span class="section-kicker">Who we are</span>
        <h2>Farmer-friendly information, dependable products and nationwide reach.</h2>
        <p>{{ $siteSettings['about_intro'] ?? 'KISANWORLD Marketing is an agriculture-focused platform based in Lahore.' }}</p>
        <p>{{ $siteSettings['about_distribution'] ?? 'We support farmers through products and practical digital agriculture information.' }}</p>
        <p>{{ $siteSettings['about_commitment'] ?? 'We aim to present clear information and responsible usage guidance.' }}</p>
    </div>
    <aside class="about-facts reveal">
        <div><strong>Lahore</strong><span>Based in Pakistan</span></div>
        <div><strong>Nationwide</strong><span>Goods transport support</span></div>
        <div><strong>Urdu + English</strong><span>Practical agriculture content</span></div>
        <div><strong>0322 6780242</strong><span>WhatsApp information & booking</span></div>
    </aside>
</section>

<section class="leader-profile section-shell">
    <div class="leader-photo reveal">
        <img src="{{ asset('logos and images/chaudhry-iftikhar-sandhu.png') }}" alt="Chaudhry Iftikhar Sandhu" loading="lazy" width="1536" height="1152">
    </div>
    <div class="leader-content reveal">
        <span class="section-kicker">Leadership Profile</span>
        <h2>Chaudhry Iftikhar Sandhu</h2>
        <p class="leader-award">Quaid-e-Azam Gold Medalist</p>
        <p class="leader-summary">Senior Journalist, TV Anchorperson, Chief Editor and agriculture media professional serving Pakistan's farming community through print, digital, television and field-focused communication.</p>

        <div class="leader-grid">
            <div class="leader-card">
                <h3>Current Roles</h3>
                <ul>
                    <li>Senior Journalist / TV Anchorperson</li>
                    <li>Chief Editor, Monthly Kisan World, Lahore</li>
                    <li>Chief Editor, Monthly Digital Kisan, Lahore</li>
                    <li>Chief Editor, Monthly Health and Education, Lahore</li>
                    <li>Chief Executive, Kisan World Marketing</li>
                    <li>Chief Executive, Kisan World TV</li>
                    <li>Director Sales & Marketing, Salar Agro Private Limited, Lahore</li>
                </ul>
            </div>
            <div class="leader-card">
                <h3>Television & Programs</h3>
                <ul>
                    <li>TV Anchorperson, Koh-e-Noor News</li>
                    <li>Weekly Agricultural Program: Kisan World with Chaudhry Iftikhar Sandhu</li>
                    <li>Live Health Program: Diabetes and Treatment with Professor Dr. Arif Riaz</li>
                    <li>Former Program Producer, PTV World</li>
                    <li>Daily Agricultural Program: Kisan Time, telecast 2003</li>
                </ul>
            </div>
            <div class="leader-card">
                <h3>Documentaries & Drama</h3>
                <ul>
                    <li>Writer, Producer and Director: Dastan-e-Istiqlal, special documentary for Pakistan Armed Forces, telecast 2003 on PTV World</li>
                    <li>Documentary Series: Sheher Sheher Gaon Gaon, telecast 2003</li>
                    <li>Playwright, Pakistan Television: Punjabi Drama Series Ajj Di Kahani, telecast 1998</li>
                </ul>
            </div>
            <div class="leader-card">
                <h3>Radio Pakistan</h3>
                <ul>
                    <li>Playwright: Bansuri Aur Phool, broadcast 1999</li>
                    <li>Playwright: Zehar Kis Ne Ghol Diya, broadcast 1999</li>
                    <li>Playwright: Sehar Qareeb Hai, broadcast 1999</li>
                    <li>Feature Writer: Aks-e-Khushbu Hoon, life and poetry of Parveen Shakir, broadcast 1999</li>
                    <li>Feature Writer: Sada Hoon Apne Pyar Ki, musical series on the life and art of Madam Noor Jehan, broadcast 1999</li>
                </ul>
            </div>
            <div class="leader-card">
                <h3>Editorial & Advisory Work</h3>
                <ul>
                    <li>Former Editor, Daily Fakhr-e-Insaniat, Lahore</li>
                    <li>Former Columnist, Daily Pakistan, Lahore</li>
                    <li>Former Columnist, Daily Jurat, Lahore</li>
                    <li>Former Columnist, Daily Musawat, Lahore</li>
                    <li>Former Columnist, Daily Nai Khabar, Lahore</li>
                    <li>Former Media Advisor, Anwar Riaz Qadeer Institute, Lahore</li>
                    <li>Former Media Advisor, Rehman Foundation, Lahore</li>
                </ul>
            </div>
            <div class="leader-card">
                <h3>Books</h3>
                <ul>
                    <li>Major Aziz Bhatti Shaheed (Nishan-e-Haider): From Birth to Martyrdom, under publication</li>
                    <li>Modern Agriculture: Changing Lives (Jadeed Zaraat, Badlay Gi Halaat)</li>
                    <li>Cotton: The Hope for Progress (Kapas: Taraqqi Ki Aas)</li>
                    <li>Record Wheat Production (Gandum Ki Record Paidawar)</li>
                    <li>Floriculture (Phoolon Ki Kasht)</li>
                    <li>Record Potato Production (Aaloo Ki Record Paidawar)</li>
                    <li>Tunnel Farming, training program</li>
                    <li>Sericulture (Silkworm Rearing)</li>
                    <li>Sugarcane: 5000 Maunds Per Acre (Kamad: 5 Hazar Man Fi Acre)</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="about-values">
    <div class="section-shell trust-grid">
        <div class="trust-intro reveal"><span class="section-kicker light">Our approach</span><h2>Clear information.<br>Responsible guidance.</h2></div>
        <div class="trust-item reveal"><strong>01</strong><h3>Useful Knowledge</h3><p>Practical agriculture articles and videos in formats farmers can easily access.</p></div>
        <div class="trust-item reveal"><strong>02</strong><h3>Product Clarity</h3><p>Pricing, stock, application information and important cautions presented clearly.</p></div>
        <div class="trust-item reveal"><strong>03</strong><h3>Direct Support</h3><p>Phone and WhatsApp contact for product information, booking and order assistance.</p></div>
    </div>
</section>
@endsection
