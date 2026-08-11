<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'special_offer_id' => ['nullable', 'exists:special_offers,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_ur' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($product?->id)],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products')->ignore($product?->id)],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'description_ur' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'manage_stock' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_top' => ['boolean'],
            'is_active' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'existing_images' => ['nullable', 'array', 'max:12'],
            'existing_images.*.id' => [
                'required', 'integer',
                Rule::exists('product_images', 'id')->where('product_id', $product?->id),
            ],
            'existing_images.*.keep' => ['nullable', 'boolean'],
            'existing_images.*.alt_text' => ['nullable', 'string', 'max:255'],
            'existing_images.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'uploads' => ['nullable', 'array', 'max:12'],
            'uploads.*.file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'uploads.*.alt_text' => ['nullable', 'string', 'max:255'],
            'uploads.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'primary_image' => ['nullable', 'string', 'max:50'],
            'og_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_og_image' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $kept = collect($this->input('existing_images', []))
                    ->filter(fn ($image) => (bool) ($image['keep'] ?? false))
                    ->count();
                $uploaded = collect($this->file('uploads', []))
                    ->filter(fn ($upload) => isset($upload['file']))
                    ->count();

                if ($kept + $uploaded > 12) {
                    $validator->errors()->add('uploads', 'A product can have a maximum of 12 images.');
                }
            },
        ];
    }
}
