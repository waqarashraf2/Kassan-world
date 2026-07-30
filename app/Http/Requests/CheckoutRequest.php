<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', 'in:cash_on_delivery,bank_transfer,online_payment'],
            'payment_proof' => ['exclude_unless:payment_method,bank_transfer', 'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'card_number' => ['prohibited'],
            'card_no' => ['prohibited'],
            'pan' => ['prohibited'],
            'cvc' => ['prohibited'],
            'cvv' => ['prohibited'],
            'expiry' => ['prohibited'],
            'expiry_date' => ['prohibited'],
            'expire_date' => ['prohibited'],
            'billing_name' => ['exclude_unless:payment_method,online_payment', 'required', 'string', 'max:120'],
            'billing_email' => ['exclude_unless:payment_method,online_payment', 'required', 'email', 'max:255'],
            'billing_phone' => ['exclude_unless:payment_method,online_payment', 'required', 'string', 'max:30'],
            'billing_city' => ['exclude_unless:payment_method,online_payment', 'nullable', 'string', 'max:120'],
            'billing_address' => ['exclude_unless:payment_method,online_payment', 'required', 'string', 'max:1000'],
            'online_payment_consent' => ['exclude_unless:payment_method,online_payment', 'required', 'accepted'],
            'create_account' => ['nullable', 'boolean'],
            'password' => ['nullable', 'required_if:create_account,1', 'confirmed', 'min:8'],
            'save_address' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('customer_email', [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email'),
        ], fn () => ! $this->user() && $this->boolean('create_account'));
    }
}
