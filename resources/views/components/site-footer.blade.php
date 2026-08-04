<footer class="site-footer">
    <div class="footer-grid">
        <div>
            <a href="{{ route('home') }}" class="footer-brand">KISAN<span>WORLD</span></a>
            <p>Better products, practical knowledge and dependable support for every growing season.</p>
        </div>
        <div><h2>Explore</h2><a href="{{ route('products.index') }}">Products</a><a href="{{ route('blogs.urdu.index') }}">Urdu Blogs</a><a href="{{ route('videos.index') }}">Videos</a></div>
        <div><h2>Support</h2><a href="{{ route('about') }}">About Us</a><a href="{{ route('contact.create') }}">Contact Us</a><a href="{{ route('magazines.index') }}">Magazine</a><a href="{{ route('cart.index') }}">Your Cart</a></div>
        <div><h2>Policies</h2><a href="{{ route('policies.privacy') }}">Privacy Policy</a><a href="{{ route('policies.terms') }}">Terms & Conditions</a><a href="{{ route('policies.shipping') }}">Shipping / Service Policy</a><a href="{{ route('policies.refund') }}">Refund / Return Policy</a></div>
        <div>
            <h2>Stay Connected</h2>
            <a href="tel:+92{{ ltrim($siteSettings['site_phone'] ?? '03226780242', '0') }}">{{ $siteSettings['site_phone'] ?? '03226780242' }}</a>
            <a href="mailto:{{ $siteSettings['site_email'] ?? 'kisanworld.magazine@gmail.com' }}">{{ $siteSettings['site_email'] ?? 'kisanworld.magazine@gmail.com' }}</a>
            <address>{{ $siteSettings['site_address'] ?? 'KISANWORLD Marketing, Lahore, Pakistan' }}</address>
            <div class="social-links" aria-label="KISANWORLD social media">
                @if(!empty($siteSettings['whatsapp_url']))
                    <a href="{{ $siteSettings['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="KISANWORLD on WhatsApp" title="WhatsApp">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.7a8 8 0 0 1-11.8 7L4 20l1.3-4A8 8 0 1 1 20 11.7Z"/><path d="M9 8.5c.3 2.2 2.2 4.2 4.5 4.8l1.2-1.2 2 .9c-.3 1.4-1.2 2.1-2.6 2.1-3.4-.2-6.4-3.1-6.8-6.4 0-1.3.7-2.2 2-2.6l1 2-1.3 1.4Z"/></svg>
                    </a>
                @endif
                @if(!empty($siteSettings['youtube_url']))
                    <a href="{{ $siteSettings['youtube_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="KISANWORLD on YouTube" title="YouTube">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12s0-4-1-5-4-1-8-1-7 0-8 1-1 5-1 5 0 4 1 5 4 1 8 1 7 0 8-1 1-5 1-5Z"/><path d="m10 9 5 3-5 3Z"/></svg>
                    </a>
                @endif
                @foreach(['facebook_url' => 'Facebook', 'instagram_url' => 'Instagram'] as $key => $label)
                    @if(!empty($siteSettings[$key]))
                        <a href="{{ $siteSettings[$key] }}" target="_blank" rel="noopener noreferrer" aria-label="KISANWORLD on {{ $label }}" title="{{ $label }}"><span>{{ substr($label, 0, 1) }}</span></a>
                    @endif
                @endforeach
            </div>
            <p lang="ur" dir="rtl">کسان کی ترقی، پاکستان کی ترقی</p>
        </div>
    </div>
    <div class="footer-bottom"><span>&copy; {{ date('Y') }} KISANWORLD. All rights reserved.</span><span>Built for Pakistan's farming community.</span></div>
</footer>
@if(!empty($siteSettings['whatsapp_url']))
    <a class="whatsapp-float" href="{{ $siteSettings['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Chat with KISANWORLD on WhatsApp">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.7a8 8 0 0 1-11.8 7L4 20l1.3-4A8 8 0 1 1 20 11.7Z"/><path d="M9 8.5c.3 2.2 2.2 4.2 4.5 4.8l1.2-1.2 2 .9c-.3 1.4-1.2 2.1-2.6 2.1-3.4-.2-6.4-3.1-6.8-6.4 0-1.3.7-2.2 2-2.6l1 2-1.3 1.4Z"/></svg>
    </a>
@endif
