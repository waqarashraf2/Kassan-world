<?php

namespace Database\Seeders;

use App\Models\ChatbotFaq;
use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class ChatbotFaqSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedGeminiSettings();

        $categories = [
            'Products' => [
                ['choosing the right farm product', 'comparing two agricultural products', 'reading a product description', 'finding product specifications', 'understanding product benefits', 'selecting a product for my crop', 'asking about product usage', 'finding a product category', 'checking product authenticity', 'getting product recommendations'],
                'Review the product page for specifications, crop suitability, pack size, price and stock. For crop-specific advice, tell us your crop, acreage, location and the problem you want to solve so our team can guide you responsibly.',
            ],
            'Pricing' => [
                ['current product price', 'price changes', 'bulk order pricing', 'price per pack', 'price including delivery', 'wholesale pricing', 'price quotation', 'tax included in price', 'price for multiple quantities', 'confirming a displayed price'],
                'Current prices are shown on each product page and in your cart. Delivery or special handling charges, when applicable, are shown before order confirmation. Contact support for a documented bulk quotation.',
            ],
            'Cart' => [
                ['adding an item to cart', 'removing a cart item', 'changing cart quantity', 'an empty shopping cart', 'cart items disappearing', 'saving cart products', 'cart total calculation', 'out of stock cart items', 'opening my shopping cart', 'adding several products'],
                'Use Add to Cart on any product, then open the cart icon to review quantities, remove items and see the total. Stock is checked again at checkout to prevent unavailable quantities from being ordered.',
            ],
            'Checkout' => [
                ['placing an order as a guest', 'creating an account at checkout', 'checkout validation error', 'entering delivery details', 'changing checkout information', 'order notes at checkout', 'checkout page not loading', 'confirming an order', 'saving an address at checkout', 'checkout security'],
                'You can check out as a guest. To create an account without leaving checkout, select the account option and set a password. Your order will be placed and linked to the new account automatically.',
            ],
            'Delivery' => [
                ['estimated delivery time', 'same day delivery', 'delivery to a village', 'delivery to another city', 'late delivery', 'delivery phone call', 'changing delivery date', 'delivery confirmation', 'missed delivery attempt', 'receiving a damaged parcel'],
                'Delivery time depends on product availability, destination and courier coverage. The team confirms the order and expected window by phone or email. Share your city and order number for a more precise update.',
            ],
            'Shipping' => [
                ['shipping charges', 'free shipping eligibility', 'courier company', 'shipping coverage', 'shipping fragile products', 'shipping heavy tools', 'shipping address change', 'combined shipment', 'separate shipments', 'shipping outside Pakistan'],
                'Shipping availability and charges depend on destination, weight and product handling requirements. Any applicable charge is confirmed before dispatch. International shipping is not assumed unless support confirms it in writing.',
            ],
            'Returns' => [
                ['starting a return', 'return eligibility', 'return time limit', 'returning an opened product', 'returning a wrong product', 'returning a damaged product', 'return pickup', 'return shipping cost', 'return proof required', 'return status'],
                'Contact support promptly with the order number, item name, reason and clear photos when relevant. Eligibility depends on product condition, safety restrictions and the issue reported. Do not use or discard a disputed item before guidance.',
            ],
            'Refunds' => [
                ['requesting a refund', 'refund processing time', 'cash on delivery refund', 'bank transfer refund', 'partial refund', 'refund after cancellation', 'missing refund', 'refund method', 'refund confirmation', 'refund for damaged goods'],
                'Approved refunds are returned through an agreed method after verification. Processing time varies by payment channel. Keep your order number and payment evidence available so support can trace the request quickly.',
            ],
            'Order Tracking' => [
                ['tracking my order', 'finding an order number', 'order still pending', 'order processing status', 'order shipped status', 'order delivered status', 'tracking without an account', 'tracking link not working', 'courier tracking number', 'order status notification'],
                'Signed-in customers can open Account, then Orders, to see the live status timeline. Guests can contact support with their order number and phone number. Status emails are sent when the order moves through fulfilment.',
            ],
            'Payments' => [
                ['cash on delivery', 'bank transfer', 'payment confirmation', 'failed payment', 'duplicate payment', 'payment receipt', 'paying before delivery', 'changing payment method', 'secure payment details', 'payment pending status'],
                'Available payment methods appear at checkout. Cash on delivery and bank transfer may be offered depending on the order. Never send payment to an unverified account; use only details confirmed by KISANWORLD.',
            ],
            'Accounts' => [
                ['creating a customer account', 'editing my profile', 'changing my email', 'changing my phone number', 'closing my account', 'account benefits', 'saving delivery addresses', 'email notification settings', 'customer dashboard', 'account privacy'],
                'Create an account from the Login page or during checkout. The dashboard lets you update profile details, save addresses, view notifications, manage wishlist items and track current and previous orders.',
            ],
            'Login' => [
                ['forgotten password', 'reset email not received', 'invalid login details', 'Google sign in', 'staying signed in', 'logging out', 'email already registered', 'password requirements', 'reset link expired', 'account login security'],
                'Use Forgot Password for a secure reset link, or Continue with Google when configured. Check spam for reset emails. For security, reset links expire and passwords should be unique and at least eight characters.',
            ],
            'Availability' => [
                ['checking stock availability', 'out of stock product', 'product restock date', 'reserving stock', 'limited quantity stock', 'availability in my city', 'discontinued product', 'preorder availability', 'bulk quantity availability', 'stock status accuracy'],
                'The product page shows the latest recorded stock status, and checkout verifies quantity again. For restock dates, reservations or bulk quantities, contact support because availability can change quickly.',
            ],
            'Discounts' => [
                ['current product discount', 'discount price calculation', 'bulk purchase discount', 'seasonal promotion', 'coupon code', 'expired discount', 'farmer group discount', 'magazine discount', 'discount eligibility', 'promotional offer terms'],
                'Active discounts are displayed beside the regular price. Promotions may have quantity, date or stock limits. A discount is valid only when shown in the order summary or confirmed by the KISANWORLD team.',
            ],
            'Fertilizers' => [
                ['fertilizer for wheat', 'fertilizer for rice', 'fertilizer for cotton', 'fertilizer pack size', 'fertilizer application timing', 'fertilizer storage', 'fertilizer compatibility', 'fertilizer dosage guidance', 'fertilizer product comparison', 'fertilizer safety'],
                'Fertilizer selection and dose depend on crop stage, soil condition, irrigation and local recommendations. Read the product label and consult a qualified agronomist for field-specific dosage. Keep products sealed and away from children.',
            ],
            'Seeds' => [
                ['wheat seed selection', 'rice seed selection', 'vegetable seeds', 'seed germination', 'seed pack size', 'seed variety suitability', 'seed storage', 'certified seed', 'seed sowing time', 'seed availability'],
                'Choose seed according to crop, region, season, water availability and desired maturity period. Check the product label for certification, lot information, germination guidance and storage instructions.',
            ],
            'Crop Protection' => [
                ['pesticide selection', 'fungicide information', 'insecticide information', 'herbicide information', 'crop medicine dosage', 'spray timing', 'protective equipment', 'pesticide storage', 'mixing crop medicines', 'pesticide safety interval'],
                'Crop protection products must be used strictly according to the approved label and local regulations. Confirm the pest or disease first, wear protective equipment, respect intervals, and seek qualified advice before mixing products.',
            ],
            'Farm Tools' => [
                ['choosing a farm tool', 'tool warranty', 'tool spare parts', 'tool maintenance', 'manual tool delivery', 'powered tool safety', 'tool replacement', 'tool specifications', 'tool availability', 'bulk tool order'],
                'Product pages list tool specifications and included parts. Follow the manufacturer safety and maintenance instructions. Contact support before purchase when compatibility, warranty, spare parts or heavy-item delivery is important.',
            ],
            'Support' => [
                ['contacting customer support', 'opening a support ticket', 'talking to a live representative', 'sending product photos', 'reporting an order issue', 'complaint handling', 'urgent assistance', 'support response time', 'contact form confirmation', 'WhatsApp support'],
                'Use live chat, the contact form, WhatsApp or a support ticket. Include your order number and a clear description. When live staff are unavailable, the FAQ assistant records the conversation for follow-up.',
            ],
            'Business Hours' => [
                ['support opening hours', 'weekend support', 'holiday hours', 'calling the office', 'best time to contact', 'after hours message', 'live chat availability', 'order processing hours', 'response outside business hours', 'office location hours'],
                'Business hours and contact details are shown on the Contact page and may change on public holidays. You can leave a chat message, contact form or support ticket at any time for the team to review.',
            ],
        ];

        $templates = [
            'How can KISANWORLD help me with %s?',
            'What should I know about %s?',
            'Please explain %s before I continue.',
            'I need guidance about %s. What should I do?',
            'Can you give me clear information about %s?',
        ];

        $rows = [];
        foreach ($categories as $category => [$subjects, $answer]) {
            foreach ($subjects as $subject) {
                foreach ($templates as $index => $template) {
                    $rows[] = [
                        'category' => $category,
                        'question' => sprintf($template, $subject),
                        'answer' => $answer,
                        'keywords' => $category.' '.$subject,
                        'priority' => 50 - $index,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        foreach (array_chunk($rows, 250) as $chunk) {
            ChatbotFaq::upsert(
                $chunk,
                ['question'],
                ['category', 'answer', 'keywords', 'priority', 'is_active', 'updated_at']
            );
        }
    }

    private function seedGeminiSettings(): void
    {
        $settings = [
            ['key' => 'gemini_auto_reply_enabled', 'value' => '0', 'type' => 'number', 'group' => 'chatbot', 'is_public' => false],
            ['key' => 'gemini_api_key', 'value' => '', 'type' => 'text', 'group' => 'chatbot', 'is_public' => false],
            ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash', 'type' => 'text', 'group' => 'chatbot', 'is_public' => false],
            ['key' => 'gemini_temperature', 'value' => '0.3', 'type' => 'number', 'group' => 'chatbot', 'is_public' => false],
            ['key' => 'gemini_max_output_tokens', 'value' => '220', 'type' => 'number', 'group' => 'chatbot', 'is_public' => false],
            [
                'key' => 'gemini_system_prompt',
                'value' => 'You are KISANWORLD Support. Reply briefly and helpfully to customers about products, prices, delivery, payments, returns, accounts, orders, magazines and support. Do not ask for card number, CVV, PIN, OTP or passwords. For pesticide, fertilizer dosage, medical, legal or financial decisions, give general safety guidance and ask the customer to contact KISANWORLD or a qualified professional. If you do not know, say so and ask for city, product name, order number or more details.',
                'type' => 'textarea',
                'group' => 'chatbot',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            WebsiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting + ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
