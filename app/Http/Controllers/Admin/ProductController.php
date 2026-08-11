<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Support\RichTextSanitizer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(private readonly RichTextSanitizer $sanitizer) {}

    public function index()
    {
        return view('admin.products.index', ['products' => Product::with('category')->latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::where('is_active', true)->get(),
            'specialOffers' => SpecialOffer::where('is_active', true)->get(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $uploads = Arr::pull($data, 'uploads', []);
            $primary = Arr::pull($data, 'primary_image');
            $ogImage = Arr::pull($data, 'og_image_file');
            Arr::forget($data, ['existing_images', 'remove_og_image']);
            $data['description'] = $this->sanitizer->sanitize($data['description'] ?? null);
            $data['description_ur'] = $this->sanitizer->sanitize($data['description_ur'] ?? null);
            if ($ogImage) {
                $data['og_image'] = $ogImage->store('uploads/products/social', 'public');
            }

            $product = Product::create($data);
            $this->storeUploadedImages($product, $uploads, $primary);

            return $product;
        });

        return redirect()->route('admin.products.edit', $product)->with('success', __('Product created.'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product' => $product->load('images'),
            'categories' => Category::where('is_active', true)->get(),
            'specialOffers' => SpecialOffer::where('is_active', true)->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product): void {
            $data = $request->validated();
            $existing = Arr::pull($data, 'existing_images', []);
            $uploads = Arr::pull($data, 'uploads', []);
            $primary = Arr::pull($data, 'primary_image');
            $ogImage = Arr::pull($data, 'og_image_file');
            $removeOgImage = (bool) Arr::pull($data, 'remove_og_image', false);
            $oldOgImage = $product->og_image;
            $data['description'] = $this->sanitizer->sanitize($data['description'] ?? null);
            $data['description_ur'] = $this->sanitizer->sanitize($data['description_ur'] ?? null);

            if ($ogImage) {
                $data['og_image'] = $ogImage->store('uploads/products/social', 'public');
            } elseif ($removeOgImage) {
                $data['og_image'] = null;
            }

            $product->update($data);

            $keptIds = collect($existing)
                ->filter(fn (array $image) => (bool) ($image['keep'] ?? false))
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            $removed = $product->images()->whereNotIn('id', $keptIds)->get();
            foreach ($removed as $image) {
                if (str_starts_with($image->path, 'uploads/')) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            }

            foreach ($existing as $imageData) {
                if (! ($imageData['keep'] ?? false)) {
                    continue;
                }
                $product->images()->whereKey($imageData['id'])->update([
                    'alt_text' => $imageData['alt_text'] ?? null,
                    'sort_order' => $imageData['sort_order'] ?? 0,
                    'is_primary' => $primary === 'existing:'.$imageData['id'],
                ]);
            }

            $this->storeUploadedImages($product, $uploads, $primary);
            $this->ensurePrimaryImage($product);

            if (($ogImage || $removeOgImage) && str_starts_with((string) $oldOgImage, 'uploads/')) {
                Storage::disk('public')->delete($oldOgImage);
            }
        });

        return back()->with('success', __('Product updated.'));
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            if (str_starts_with($image->path, 'uploads/')) {
                Storage::disk('public')->delete($image->path);
            }
        }
        if (str_starts_with((string) $product->og_image, 'uploads/')) {
            Storage::disk('public')->delete($product->og_image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', __('Product deleted.'));
    }

    private function storeUploadedImages(Product $product, array $uploads, ?string $primary): void
    {
        foreach ($uploads as $index => $upload) {
            if (! isset($upload['file'])) {
                continue;
            }

            $product->images()->create([
                'path' => $upload['file']->store('uploads/products/'.$product->id, 'public'),
                'alt_text' => $upload['alt_text'] ?? $product->name,
                'sort_order' => $upload['sort_order'] ?? $index,
                'is_primary' => $primary === 'new:'.$index,
            ]);
        }

        $this->ensurePrimaryImage($product);
    }

    private function ensurePrimaryImage(Product $product): void
    {
        if ($product->images()->exists() && ! $product->images()->where('is_primary', true)->exists()) {
            $product->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }
    }
}
