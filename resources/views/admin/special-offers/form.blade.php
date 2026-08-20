@php($editingOffer = $specialOffer ?? null)
<section class="admin-form-card">
    <div class="admin-card-head">
        <div>
            <h2>Basic Information</h2>
            <p>Define the identity and details of this seasonal/festival offer.</p>
        </div>
    </div>
    <div class="admin-field-grid">
        <label>Offer Name<input name="name" value="{{ old('name', $editingOffer?->name) }}" required></label>
        <label>Urdu Name<input name="name_ur" value="{{ old('name_ur', $editingOffer?->name_ur) }}" dir="rtl"></label>
        <label class="admin-span-2">Slug <small>Leave blank for automatic SEO slug.</small><input name="slug" value="{{ old('slug', $editingOffer?->slug) }}"></label>
        
        <label>Discount Percentage (%) <small>Optional discount rate to display</small>
            <input type="number" min="0" max="100" name="discount_percentage" value="{{ old('discount_percentage', $editingOffer?->discount_percentage) }}">
        </label>
        
        <label class="admin-check-field">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editingOffer?->is_active ?? true))> Active Promotion
        </label>

        <label>Start Date<input type="date" name="start_date" value="{{ old('start_date', $editingOffer?->start_date?->format('Y-m-d')) }}"></label>
        <label>End Date<input type="date" name="end_date" value="{{ old('end_date', $editingOffer?->end_date?->format('Y-m-d')) }}"></label>

        <div class="admin-span-2">
            <span class="admin-field-label">English Description</span>
            <x-admin.rich-text-editor name="description" :value="old('description', $editingOffer?->description)" />
        </div>
        
        <div class="admin-span-2">
            <span class="admin-field-label">Urdu Description</span>
            <x-admin.rich-text-editor name="description_ur" :value="old('description_ur', $editingOffer?->description_ur)" dir="rtl" />
        </div>
    </div>
</section>

<section class="admin-form-card">
    <div class="admin-card-head">
        <div>
            <h2>Banner / Promotion Art</h2>
            <p>Upload a promotional image/banner to showcase this offer.</p>
        </div>
    </div>
    <div class="admin-field-grid">
        <label>Banner Image <small>JPG, PNG, or WebP. Max 5MB.</small>
            <input type="file" name="banner_image_file" accept="image/jpeg,image/png,image/webp">
        </label>
        
        @if($editingOffer?->banner_image)
            <div class="admin-current-file admin-span-2">
                <span>Current Promotional Banner</span>
                <img src="{{ $editingOffer->banner_image_url }}" alt="" style="max-width: 300px; display: block; border-radius: 8px; margin-top: 8px;">
                <label class="admin-check-field" style="margin-top: 8px;">
                    <input type="checkbox" name="remove_banner_image" value="1"> Remove Banner
                </label>
            </div>
        @endif
    </div>
</section>

@php
    $selectedProductIds = old('product_ids', $editingOffer?->products ? $editingOffer->products->pluck('id')->toArray() : []);
@endphp
<section class="admin-form-card">
    <div class="admin-card-head">
        <div>
            <h2>Assigned Products</h2>
            <p>Select products included in this special offer (will appear on the homepage and show offer badges).</p>
        </div>
    </div>
    <div style="margin-bottom: 1rem;">
        <input type="text" id="offerProductSearch" placeholder="Type to filter products..." style="max-width: 400px; padding: 0.5rem 0.8rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
    </div>
    <div class="admin-field-grid" id="offerProductsList" style="max-height: 350px; overflow-y: auto; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 8px; background: #fafafa;">
        @forelse($products ?? [] as $product)
            <label class="admin-check-field product-item-filter" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: #fff; border: 1px solid #e5e7eb; border-radius: 6px;" data-name="{{ strtolower($product->name) }}">
                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" @checked(in_array($product->id, $selectedProductIds))>
                <span>
                    <strong>{{ $product->name }}</strong>
                    <small style="color: #6b7280; margin-left: 0.5rem;">(Rs. {{ number_format($product->price, 0) }})</small>
                    @if($product->special_offer_id && $product->special_offer_id !== $editingOffer?->id)
                        <small style="color: #e67e22; margin-left: 0.5rem;">[Currently in another offer]</small>
                    @endif
                </span>
            </label>
        @empty
            <p style="color: #6b7280; font-size: 0.9rem;">No products available yet.</p>
        @endforelse
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('offerProductSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('#offerProductsList .product-item-filter').forEach(item => {
                const name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(query) ? 'flex' : 'none';
            });
        });
    }
});
</script>
