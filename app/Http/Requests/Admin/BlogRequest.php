<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $blog = $this->route('blog');

        return [
            'blog_category_id' => [
                'required',
                Rule::exists('blog_categories', 'id')->where(fn ($query) => $query
                    ->where('language', $this->input('language'))
                    ->where('is_active', true)
                    ->whereNull('deleted_at')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255',
                Rule::unique('blogs')->where(fn ($query) => $query->where('language', $this->input('language')))->ignore($blog?->id),
            ],
            'language' => ['required', 'in:en,ur'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'featured_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
