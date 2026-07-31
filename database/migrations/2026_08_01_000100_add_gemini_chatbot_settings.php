<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['key' => 'gemini_auto_reply_enabled', 'value' => '0', 'type' => 'number'],
            ['key' => 'gemini_api_key', 'value' => '', 'type' => 'text'],
            ['key' => 'gemini_model', 'value' => 'gemini-2.5-flash', 'type' => 'text'],
            ['key' => 'gemini_temperature', 'value' => '0.3', 'type' => 'number'],
            ['key' => 'gemini_max_output_tokens', 'value' => '220', 'type' => 'number'],
            [
                'key' => 'gemini_system_prompt',
                'value' => 'You are KISANWORLD Support. Reply briefly and helpfully to customers about products, prices, delivery, payments, returns, accounts, orders, magazines and support. Do not ask for card number, CVV, PIN, OTP or passwords. For pesticide, fertilizer dosage, medical, legal or financial decisions, give general safety guidance and ask the customer to contact KISANWORLD or a qualified professional. If you do not know, say so and ask for city, product name, order number or more details.',
                'type' => 'textarea',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('website_settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => 'chatbot',
                    'is_public' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('website_settings')
            ->whereIn('key', [
                'gemini_auto_reply_enabled',
                'gemini_api_key',
                'gemini_model',
                'gemini_temperature',
                'gemini_max_output_tokens',
                'gemini_system_prompt',
            ])
            ->delete();
    }
};
