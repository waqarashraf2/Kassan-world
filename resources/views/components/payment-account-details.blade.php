@props([
    'intro' => 'Copy any account number below, send payment, then upload the payment screenshot or receipt.',
])

@php
    $paymentAccounts = [
        [
            'method' => 'JazzCash and Raast',
            'badge' => 'JC',
            'title' => 'IFTIKHAR AHMED',
            'account' => '03004816719',
        ],
        [
            'method' => 'Easypaisa',
            'badge' => 'EP',
            'title' => 'IFTIKHAR AHMED',
            'account' => '03004816719',
        ],
        [
            'method' => 'Bank Alfalah',
            'badge' => 'BA',
            'title' => 'KISAN WORLD',
            'account' => '55265000698248',
            'iban' => 'PK52ALFH5526005000698248',
        ],
        [
            'method' => 'Bank Alfalah',
            'badge' => 'BA',
            'title' => 'IFTIKHAR AHMED',
            'account' => '55265001408761',
            'iban' => 'PK85ALFH5526005001408761',
        ],
        [
            'method' => 'Nayapay',
            'badge' => 'NP',
            'title' => 'IFTIKHAR AHMED',
            'account' => '03226780242',
        ],
    ];
@endphp

<div><span>KISAN WORLD account details</span><p>{{ $intro }}</p></div>
<div class="payment-account-list">
    @foreach($paymentAccounts as $account)
        <article class="payment-account-card">
            <div class="payment-account-badge">{{ $account['badge'] }}</div>
            <div class="payment-account-info">
                <strong>{{ $account['method'] }}</strong>
                <span>Account title: {{ $account['title'] }}</span>
                <div class="copy-field">
                    <code>{{ $account['account'] }}</code>
                    <button type="button" data-copy-value="{{ $account['account'] }}">Copy</button>
                </div>
                @isset($account['iban'])
                    <div class="copy-field">
                        <code>{{ $account['iban'] }}</code>
                        <button type="button" data-copy-value="{{ $account['iban'] }}">Copy IBAN</button>
                    </div>
                @endisset
            </div>
        </article>
    @endforeach
</div>
<p class="payment-safe-note"><strong>Note:</strong> After payment, upload screenshot here or send it on WhatsApp 0322-6780242 for quick verification.</p>
