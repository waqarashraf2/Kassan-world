<?php

namespace App\Services;

use App\Models\ChatbotFaq;
use App\Models\Product;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotService
{
    public function answer(string $question, ?User $user = null): array
    {
        $terms = $this->terms($question);
        if ($terms->isEmpty()) {
            return $this->geminiAnswer($question) ?? $this->fallback();
        }

        if ($user && preg_match('/KW-[A-Z0-9-]+/i', $question, $match)) {
            $order = $user->orders()->where('order_number', strtoupper($match[0]))->first();
            if ($order) {
                return [
                    'answer' => "Order {$order->order_number} is currently {$order->status}. You can view its full timeline here: ".route('customer.orders.show', $order),
                    'faq_id' => null,
                    'matched' => true,
                ];
            }
        }

        $product = Product::query()->active()->with('images')
            ->where(function ($query) use ($terms): void {
                foreach ($terms->take(6) as $term) {
                    $query->orWhere('name', 'like', '%'.$term.'%')
                        ->orWhere('name_ur', 'like', '%'.$term.'%')
                        ->orWhere('slug', 'like', '%'.$term.'%');
                }
            })
            ->get()
            ->map(function (Product $product) use ($terms): array {
                $name = Str::lower($product->name.' '.$product->name_ur.' '.$product->slug);
                return [
                    'product' => $product,
                    'matches' => $terms->filter(fn ($term) => Str::contains($name, $term))->count(),
                ];
            })
            ->sortByDesc('matches')
            ->first();

        if ($product && $product['matches'] >= min(2, $terms->count())) {
            $item = $product['product'];
            $stock = $item->in_stock ? 'in stock' : 'currently out of stock';

            return [
                'answer' => "{$item->name} is {$stock} at Rs. ".number_format((float) $item->sale_price).'. View details: '.route('products.show', $item),
                'faq_id' => null,
                'matched' => true,
            ];
        }

        $candidates = ChatbotFaq::query()
            ->where('is_active', true)
            ->where(function ($query) use ($terms): void {
                foreach ($terms->take(8) as $term) {
                    $query->orWhere('question', 'like', '%'.$term.'%')
                        ->orWhere('keywords', 'like', '%'.$term.'%');
                }
            })
            ->orderByDesc('priority')
            ->limit(40)
            ->get();

        $best = $candidates->map(function (ChatbotFaq $faq) use ($terms): array {
            $haystack = Str::lower($faq->question.' '.$faq->keywords.' '.$faq->category);
            $matched = $terms->filter(fn (string $term) => Str::contains($haystack, $term))->count();
            $score = ($matched / max(1, $terms->count())) + min($faq->priority, 100) / 1000;

            return compact('faq', 'score', 'matched');
        })->sortByDesc('score')->first();

        if (! $best || $best['matched'] === 0 || $best['score'] < .22) {
            return $this->geminiAnswer($question) ?? $this->fallback();
        }

        $best['faq']->increment('usage_count');

        return [
            'answer' => $best['faq']->answer,
            'faq_id' => $best['faq']->id,
            'matched' => true,
        ];
    }

    public function adminOnline(): bool
    {
        return User::where('role', 'admin')
            ->where('chat_available', true)
            ->where('last_seen_at', '>=', now()->subMinutes(2))
            ->exists();
    }

    private function fallback(): array
    {
        return [
            'answer' => 'I could not find an exact answer yet. Please choose live support and our team will help, or share your product name, order number, city, or question in a little more detail.',
            'faq_id' => null,
            'matched' => false,
        ];
    }

    private function geminiAnswer(string $question): ?array
    {
        if (! $this->settingEnabled('gemini_auto_reply_enabled')) {
            return null;
        }

        $apiKey = $this->setting('gemini_api_key') ?: config('services.gemini.api_key');
        if (! $apiKey) {
            return null;
        }

        $model = $this->setting('gemini_model') ?: config('services.gemini.model', 'gemini-2.5-flash');
        $temperature = (float) ($this->setting('gemini_temperature') ?: 0.3);
        $maxTokens = (int) ($this->setting('gemini_max_output_tokens') ?: 220);
        $systemPrompt = $this->setting('gemini_system_prompt') ?: 'You are KISANWORLD Support. Reply briefly and safely.';

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->post('https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent?key='.rawurlencode($apiKey), [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [[
                        'role' => 'user',
                        'parts' => [['text' => $question]],
                    ]],
                    'generationConfig' => [
                        'temperature' => max(0, min(1, $temperature)),
                        'maxOutputTokens' => max(50, min(600, $maxTokens)),
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('Gemini auto-reply request failed.', ['status' => $response->status()]);

                return null;
            }

            $answer = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text'));
            if ($answer === '') {
                return null;
            }

            return [
                'answer' => Str::limit($answer, 1200, ''),
                'faq_id' => null,
                'matched' => true,
            ];
        } catch (\Throwable $exception) {
            Log::warning('Gemini auto-reply could not be generated.', ['error' => $exception->getMessage()]);

            return null;
        }
    }

    private function setting(string $key): ?string
    {
        return WebsiteSetting::where('key', $key)->value('value');
    }

    private function settingEnabled(string $key): bool
    {
        return in_array(Str::lower((string) $this->setting($key)), ['1', 'true', 'yes', 'on'], true);
    }

    private function terms(string $question): Collection
    {
        $stopWords = ['the', 'a', 'an', 'is', 'are', 'to', 'for', 'of', 'and', 'or', 'my', 'i', 'me', 'can', 'do', 'how', 'what', 'when', 'where', 'please', 'kya', 'hai', 'ka', 'ki', 'ke', 'mein'];

        return collect(preg_split('/[^\pL\pN]+/u', Str::lower($question)))
            ->filter(fn (?string $term) => $term && mb_strlen($term) > 2 && ! in_array($term, $stopWords, true))
            ->unique()
            ->values();
    }
}
