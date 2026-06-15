<section class="chat-widget" data-chat-widget data-message-url="{{ route('chat.message') }}" data-live-url-template="{{ url('/chat/__CONVERSATION__/live') }}" data-poll-url-template="{{ url('/chat/__CONVERSATION__/messages') }}">
    <button class="chat-launcher" type="button" data-chat-toggle aria-expanded="false" aria-controls="kisan-chat">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h9a4 4 0 0 1 4 4Z"/><path d="M8 9h8M8 13h5"/></svg>
        <span>Ask KISANWORLD</span>
    </button>
    <div id="kisan-chat" class="chat-panel" data-chat-panel hidden>
        <header><div><span class="chat-status-dot"></span><strong>KISANWORLD Support</strong><small data-chat-status>FAQ assistant ready</small></div><button type="button" data-chat-close aria-label="Close chat">&times;</button></header>
        <div class="chat-quick">
            <button type="button" data-chat-question="How long does delivery take?">Delivery time</button>
            <button type="button" data-chat-question="How can I track my order?">Track order</button>
            <button type="button" data-chat-question="Which payment methods are available?">Payments</button>
            <button type="button" data-chat-question="What is your return policy?">Returns</button>
        </div>
        <div class="chat-messages" data-chat-messages aria-live="polite"><div class="chat-message bot"><span>K</span><p>Assalam-o-Alaikum. Ask me about products, prices, delivery, payments, returns, accounts, or order tracking.</p></div></div>
        <button type="button" class="chat-live-button" data-chat-live>Talk to a live representative</button>
        <form data-chat-form><label class="sr-only" for="chat-message">Your message</label><textarea id="chat-message" name="message" rows="1" maxlength="2000" placeholder="Type your question..." required></textarea><button type="submit" aria-label="Send message"><svg viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg></button></form>
    </div>
</section>
