<?php

namespace App\Services;

use App\Models\ChatbotFaq;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ChatbotService
{
    public function answer(string $question, ?User $user = null): array
    {
        $terms = $this->terms($question);
        if ($terms->isEmpty()) {
            return $this->fallback();
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
            return $this->fallback();
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

    private function terms(string $question): Collection
    {
        $stopWords = ['the', 'a', 'an', 'is', 'are', 'to', 'for', 'of', 'and', 'or', 'my', 'i', 'me', 'can', 'do', 'how', 'what', 'when', 'where', 'please', 'kya', 'hai', 'ka', 'ki', 'ke', 'mein'];

        return collect(preg_split('/[^\pL\pN]+/u', Str::lower($question)))
            ->filter(fn (?string $term) => $term && mb_strlen($term) > 2 && ! in_array($term, $stopWords, true))
            ->unique()
            ->values();
    }
}
