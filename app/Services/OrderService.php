<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function place(array $data, mixed $user = null): Order
    {
        return DB::transaction(function () use ($data, $user): Order {
            $items = collect($data['items'])->map(function (array $item): array {
                $product = Product::query()->active()->lockForUpdate()->findOrFail($item['product_id']);

                if (! $product->in_stock || ($product->manage_stock && $product->stock_quantity < $item['quantity'])) {
                    throw ValidationException::withMessages([
                        'items' => __('Insufficient stock for :product.', ['product' => $product->name]),
                    ]);
                }

                return compact('product') + ['quantity' => $item['quantity']];
            });

            $subtotal = $items->sum(fn ($item) => (float) $item['product']->sale_price * $item['quantity']);
            $shipping = 0;

            $order = Order::create([
                'order_number' => 'KW-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'user_id' => $user?->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'city' => $data['city'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_total' => $shipping,
                'grand_total' => $subtotal + $shipping,
                'payment_method' => $data['payment_method'],
                'payment_details' => $this->paymentDetails($data),
                'payment_proof_path' => $this->paymentProofPath($data),
                'payment_proof_original_name' => ($data['payment_proof'] ?? null)?->getClientOriginalName(),
                'placed_at' => now(),
            ]);

            foreach ($items as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit_price' => $product->sale_price,
                    'quantity' => $quantity,
                    'line_total' => (float) $product->sale_price * $quantity,
                ]);

                if ($product->manage_stock) {
                    $product->decrement('stock_quantity', $quantity);
                }
            }

            $order->statusEvents()->create([
                'status' => 'pending',
                'note' => 'Order received.',
                'occurred_at' => now(),
            ]);

            return $order->load('items');
        });
    }

    private function paymentDetails(array $data): ?array
    {
        if (($data['payment_method'] ?? null) !== 'online_payment') {
            return null;
        }

        return [
            'gateway' => 'bank_alfalah',
            'billing_name' => $data['billing_name'] ?? null,
            'billing_email' => $data['billing_email'] ?? null,
            'billing_phone' => $data['billing_phone'] ?? null,
            'billing_city' => $data['billing_city'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'card_collection' => 'redirect_gateway_only',
            'note' => 'No card number, CVV, PIN, OTP or gateway secret is stored by KISANWORLD.',
        ];
    }

    private function paymentProofPath(array $data): ?string
    {
        if (($data['payment_method'] ?? null) !== 'bank_transfer' || empty($data['payment_proof'])) {
            return null;
        }

        return $data['payment_proof']->store('payment-proofs/orders', 'local');
    }
}
