@php($editingBlog=$blog ?? null)
<section class="admin-form-card">
    <div class="admin-card-head"><div><h2>Article content</h2><p>Use the lightweight editor for clean, readable article formatting.</p></div></div>
    <div class="admin-field-grid">
        <label>Language<select name="language" required><option value="en" @selected(old('language',$editingBlog?->language ?? 'en')==='en')>English</option><option value="ur" @selected(old('language',$editingBlog?->language)==='ur')>Urdu</option></select></label>
        <label>Category<select name="blog_category_id" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('blog_category_id',$editingBlog?->blog_category_id)===$category->id)>{{ $category->name }} ({{ strtoupper($category->language) }})</option>@endforeach</select></label>
        <label class="admin-span-2">Title<input name="title" value="{{ old('title',$editingBlog?->title) }}" required></label>
        <label class="admin-span-2">Slug <small>Leave blank to create it from the title.</small><input name="slug" value="{{ old('slug',$editingBlog?->slug) }}"></label>
        <label class="admin-span-2">Excerpt<textarea name="excerpt" rows="4">{{ old('excerpt',$editingBlog?->excerpt) }}</textarea></label>
        <div class="admin-span-2">
            <span class="admin-field-label">Article content</span>
            <x-admin.rich-text-editor name="content" :value="old('content',$editingBlog?->content)" :dir="old('language',$editingBlog?->language)==='ur' ? 'rtl' : 'ltr'" required />
        </div>
    </div>
</section>
<section class="admin-form-card">
    <div class="admin-card-head"><div><h2>Publishing and featured image</h2><p>The uploaded image is used on blog cards, the detail page and social schema.</p></div></div>
    <div class="admin-field-grid">
        <label>Status<select name="status"><option value="draft" @selected(old('status',$editingBlog?->status ?? 'draft')==='draft')>Draft</option><option value="published" @selected(old('status',$editingBlog?->status)==='published')>Published</option><option value="archived" @selected(old('status',$editingBlog?->status)==='archived')>Archived</option></select></label>
        <label>Published at <small>Leave blank to publish immediately. Use a future time to schedule.</small><input type="datetime-local" name="published_at" value="{{ old('published_at',$editingBlog?->published_at?->format('Y-m-d\TH:i')) }}"></label>
        <label>Featured image<input type="file" name="featured_image_file" accept="image/jpeg,image/png,image/webp" data-image-input></label>
        <label>Featured image alt text<input name="featured_image_alt" value="{{ old('featured_image_alt',$editingBlog?->featured_image_alt) }}" placeholder="Describe the image for accessibility and SEO"></label>
        <div class="admin-upload-preview admin-span-2 {{ $editingBlog?->featured_image_url ? 'has-image' : '' }}" data-image-preview>
            @if($editingBlog?->featured_image_url)<img src="{{ $editingBlog->featured_image_url }}" alt="{{ $editingBlog->featured_image_alt ?: $editingBlog->title }}">@else<span>Featured image preview</span>@endif
        </div>
        @if($editingBlog?->featured_image)
            <label class="admin-check-field admin-span-2"><input type="checkbox" name="remove_featured_image" value="1"> Remove current featured image</label>
        @endif
    </div>
</section>
<section class="admin-form-card">
    <div class="admin-card-head"><div><h2>SEO</h2></div></div>
    <div class="admin-field-grid">
        <label>Meta title<input name="meta_title" value="{{ old('meta_title',$editingBlog?->meta_title) }}"></label>
        <label>Canonical URL<input type="url" name="canonical_url" value="{{ old('canonical_url',$editingBlog?->canonical_url) }}"></label>
        <label class="admin-span-2">Meta description<textarea name="meta_description" rows="4">{{ old('meta_description',$editingBlog?->meta_description) }}</textarea></label>
    </div>
</section>
