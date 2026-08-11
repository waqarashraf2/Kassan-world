<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Magazine;
use App\Models\Product;
use App\Models\SpecialOffer;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'products' => $this->productQuery()->paginate(5),
            'topProducts' => Product::active()
                ->where('is_top', true)
                ->with(['category:id,name,slug', 'images:id,product_id,path,alt_text,is_primary,sort_order'])
                ->latest()
                ->take(8)
                ->get(),
            'specialOffers' => SpecialOffer::active()
                ->with(['products' => function ($q) {
                    $q->active()->with(['category:id,name,slug', 'images:id,product_id,path,alt_text,is_primary,sort_order']);
                }])
                ->latest()
                ->get(),
            'latestBlogs' => Blog::published()->with(['category', 'author'])->latest('published_at')->take(6)->get(),
            'latestVideos' => Video::published()->latest('published_at')->take(6)->get(),
            'latestMagazines' => Magazine::active()->latest('issue_date')->take(4)->get(),
        ]);
    }

    public function products(Request $request)
    {
        $products = $this->productQuery()->paginate(5);

        return response()->json([
            'html' => view('components.product-batch', compact('products'))->render(),
            'next_page_url' => $products->nextPageUrl(),
        ]);
    }

    private function productQuery()
    {
        return Product::active()
            ->with(['category:id,name,slug', 'images:id,product_id,path,alt_text,is_primary,sort_order'])
            ->orderByDesc('is_featured')
            ->latest();
    }
}
