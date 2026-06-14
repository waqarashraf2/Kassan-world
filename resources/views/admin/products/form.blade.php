@php($editingProduct = $product ?? null)
<section class="admin-form-card">
    <div class="admin-card-head"><div><h2>Basic information</h2><p>English and Urdu product identity.</p></div></div>
    <div class="admin-field-grid">
        <label>Category<select name="category_id" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$editingProduct?->category_id)===$category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label>SKU<input name="sku" value="{{ old('sku',$editingProduct?->sku) }}" maxlength="100"></label>
        <label>Product name<input name="name" value="{{ old('name',$editingProduct?->name) }}" required></label>
        <label>Urdu name<input name="name_ur" value="{{ old('name_ur',$editingProduct?->name_ur) }}" dir="rtl"></label>
        <label class="admin-span-2">Slug <small>Leave blank for automatic SEO slug.</small><input name="slug" value="{{ old('slug',$editingProduct?->slug) }}"></label>
        <label class="admin-span-2">Short description<textarea name="short_description" rows="3">{{ old('short_description',$editingProduct?->short_description) }}</textarea></label>
        <div class="admin-span-2">
            <span class="admin-field-label">English description</span>
            <x-admin.rich-text-editor name="description" :value="old('description',$editingProduct?->description)" />
        </div>
        <div class="admin-span-2">
            <span class="admin-field-label">Urdu description</span>
            <x-admin.rich-text-editor name="description_ur" :value="old('description_ur',$editingProduct?->description_ur)" dir="rtl" />
        </div>
    </div>
</section>

<section class="admin-form-card">
    <div class="admin-card-head"><div><h2>Pricing and stock</h2><p>Customer-facing price and inventory controls.</p></div></div>
    <div class="admin-field-grid admin-field-grid-4">
        <label>Regular price<input type="number" step="0.01" min="0" name="price" value="{{ old('price',$editingProduct?->price) }}" required></label>
        <label>Discount price<input type="number" step="0.01" min="0" name="discount_price" value="{{ old('discount_price',$editingProduct?->discount_price) }}"></label>
        <label>Stock quantity<input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity',$editingProduct?->stock_quantity ?? 0) }}" required></label>
        <label class="admin-check-field"><input type="hidden" name="manage_stock" value="0"><input type="checkbox" name="manage_stock" value="1" @checked(old('manage_stock',$editingProduct?->manage_stock ?? true))> Manage stock</label>
        <label class="admin-check-field"><input type="hidden" name="is_featured" value="0"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$editingProduct?->is_featured))> Featured product</label>
        <label class="admin-check-field"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$editingProduct?->is_active ?? true))> Visible on website</label>
    </div>
</section>

<section class="admin-form-card">
    <div class="admin-card-head"><div><h2>Product gallery</h2><p>Upload JPG, PNG or WebP images, maximum 5 MB each and 12 images per product.</p></div></div>
    @if($editingProduct?->images?->isNotEmpty())
        <div class="admin-media-grid">
            @foreach($editingProduct->images as $index => $image)
                <article class="admin-media-item">
                    <img src="{{ $image->url }}" alt="{{ $image->alt_text ?: $editingProduct->name }}">
                    <input type="hidden" name="existing_images[{{ $index }}][id]" value="{{ $image->id }}">
                    <input type="hidden" name="existing_images[{{ $index }}][keep]" value="0">
                    <label class="admin-check-field"><input type="checkbox" name="existing_images[{{ $index }}][keep]" value="1" checked> Keep image</label>
                    <label>Alt text<input name="existing_images[{{ $index }}][alt_text]" value="{{ old("existing_images.$index.alt_text",$image->alt_text) }}"></label>
                    <label>Order<input type="number" min="0" name="existing_images[{{ $index }}][sort_order]" value="{{ old("existing_images.$index.sort_order",$image->sort_order) }}"></label>
                    <label class="admin-check-field"><input type="radio" name="primary_image" value="existing:{{ $image->id }}" @checked(old('primary_image',$image->is_primary ? 'existing:'.$image->id : null)==='existing:'.$image->id)> Primary image</label>
                </article>
            @endforeach
        </div>
    @endif
    <div class="admin-upload-rows" data-upload-rows data-max-files="{{ max(0, 12 - ($editingProduct?->images?->count() ?? 0)) }}">
        <div class="admin-upload-row" data-upload-row>
            <label>Upload image<input type="file" name="uploads[0][file]" accept="image/jpeg,image/png,image/webp" data-image-input></label>
            <div class="admin-upload-preview" data-image-preview><span>Preview</span></div>
            <label>Alt text<input name="uploads[0][alt_text]" placeholder="Describe the product image"></label>
            <label>Order<input type="number" min="0" name="uploads[0][sort_order]" value="{{ $editingProduct?->images?->count() ?? 0 }}"></label>
            <label class="admin-check-field"><input type="radio" name="primary_image" value="new:0"> Primary image</label>
        </div>
    </div>
    <button type="button" class="admin-secondary" data-add-upload-row>Add another image</button>
</section>

<section class="admin-form-card">
    <div class="admin-card-head"><div><h2>SEO</h2><p>Search and social sharing metadata.</p></div></div>
    <div class="admin-field-grid">
        <label>Meta title<input name="meta_title" value="{{ old('meta_title',$editingProduct?->meta_title) }}" maxlength="255"></label>
        <label>Canonical URL<input type="url" name="canonical_url" value="{{ old('canonical_url',$editingProduct?->canonical_url) }}"></label>
        <label class="admin-span-2">Meta description<textarea name="meta_description" rows="3" maxlength="500">{{ old('meta_description',$editingProduct?->meta_description) }}</textarea></label>
        <label>Social sharing image<input type="file" name="og_image_file" accept="image/jpeg,image/png,image/webp"></label>
        @if($editingProduct?->og_image)
            <div class="admin-current-file"><span>Current social image</span><img src="{{ $editingProduct->og_image_url }}" alt=""><label class="admin-check-field"><input type="checkbox" name="remove_og_image" value="1"> Remove</label></div>
        @endif
    </div>
</section>
