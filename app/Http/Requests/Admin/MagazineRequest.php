<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MagazineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $magazine = $this->route('magazine');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('magazines')->ignore($magazine?->id)],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'cover_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cover_image_alt' => ['nullable', 'string', 'max:255'],
            'pdf_path' => ['required_without:pdf_upload', 'nullable', 'string', 'max:255'],
            'pdf_upload' => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_free' => ['boolean'],
            'allow_download' => ['boolean'],
            'is_active' => ['boolean'],
            'issue_date' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
