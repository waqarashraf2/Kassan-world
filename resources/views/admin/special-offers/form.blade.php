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
