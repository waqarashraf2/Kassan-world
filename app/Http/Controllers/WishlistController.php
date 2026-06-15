<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        return view('customer.wishlist', [
            'products' => $request->user()->wishlistProducts()->active()->with('images')->latest('wishlists.created_at')->paginate(12),
        ]);
    }

    public function toggle(Request $request, Product $product)
    {
        $attached = $request->user()->wishlistProducts()->whereKey($product->id)->exists();
        $attached
            ? $request->user()->wishlistProducts()->detach($product)
            : $request->user()->wishlistProducts()->attach($product);

        if ($request->expectsJson()) {
            return response()->json(['saved' => ! $attached]);
        }

        return back()->with('success', $attached ? __('Removed from wishlist.') : __('Saved to wishlist.'));
    }
}
