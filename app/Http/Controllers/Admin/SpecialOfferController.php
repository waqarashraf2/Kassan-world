<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SpecialOfferRequest;
use App\Models\Product;
use App\Models\SpecialOffer;
use App\Support\RichTextSanitizer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SpecialOfferController extends Controller
{
    public function __construct(private readonly RichTextSanitizer $sanitizer) {}

    public function index()
    {
        return view('admin.special-offers.index', [
            'specialOffers' => SpecialOffer::withCount('products')->latest()->paginate(20)
        ]);
    }

    public function create()
    {
        return view('admin.special-offers.create', [
            'products' => Product::orderBy('name')->get(['id', 'name', 'price', 'special_offer_id']),
        ]);
    }

    public function store(SpecialOfferRequest $request)
    {
        $data = $request->validated();
        $bannerFile = Arr::pull($data, 'banner_image_file');
        $productIds = Arr::pull($data, 'product_ids', []);
        
        $data['description'] = $this->sanitizer->sanitize($data['description'] ?? null);
        $data['description_ur'] = $this->sanitizer->sanitize($data['description_ur'] ?? null);

        if ($bannerFile) {
            $data['banner_image'] = $bannerFile->store('uploads/special-offers', 'public');
        }

        $specialOffer = SpecialOffer::create($data);

        if (!empty($productIds)) {
            Product::whereIn('id', $productIds)->update(['special_offer_id' => $specialOffer->id]);
        }

        return redirect()->route('admin.special-offers.edit', $specialOffer)->with('success', __('Special offer created.'));
    }

    public function edit(SpecialOffer $specialOffer)
    {
        $specialOffer->load('products');
        $products = Product::orderBy('name')->get(['id', 'name', 'price', 'special_offer_id']);

        return view('admin.special-offers.edit', compact('specialOffer', 'products'));
    }

    public function update(SpecialOfferRequest $request, SpecialOffer $specialOffer)
    {
        $data = $request->validated();
        $bannerFile = Arr::pull($data, 'banner_image_file');
        $productIds = Arr::pull($data, 'product_ids', []);
        
        $data['description'] = $this->sanitizer->sanitize($data['description'] ?? null);
        $data['description_ur'] = $this->sanitizer->sanitize($data['description_ur'] ?? null);

        if ($bannerFile) {
            if ($specialOffer->banner_image) {
                Storage::disk('public')->delete($specialOffer->banner_image);
            }
            $data['banner_image'] = $bannerFile->store('uploads/special-offers', 'public');
        } elseif ($request->boolean('remove_banner_image')) {
            if ($specialOffer->banner_image) {
                Storage::disk('public')->delete($specialOffer->banner_image);
            }
            $data['banner_image'] = null;
        }

        $specialOffer->update($data);

        // Remove association from products that were unchecked
        Product::where('special_offer_id', $specialOffer->id)
            ->whereNotIn('id', $productIds)
            ->update(['special_offer_id' => null]);

        // Assign newly selected products to this offer
        if (!empty($productIds)) {
            Product::whereIn('id', $productIds)->update(['special_offer_id' => $specialOffer->id]);
        }

        return back()->with('success', __('Special offer updated.'));
    }

    public function destroy(SpecialOffer $specialOffer)
    {
        if ($specialOffer->banner_image) {
            Storage::disk('public')->delete($specialOffer->banner_image);
        }
        
        // This will set special_offer_id to null on products via the DB level nullOnDelete foreign key constraint
        $specialOffer->delete();

        return redirect()->route('admin.special-offers.index')->with('success', __('Special offer deleted.'));
    }
}
