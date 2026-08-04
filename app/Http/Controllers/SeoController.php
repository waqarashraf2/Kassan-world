<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Magazine;
use App\Models\Product;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    public function sitemap()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function (): string {
            $urls = collect([
                ['loc' => route('home'), 'lastmod' => now()],
                ['loc' => route('products.index'), 'lastmod' => now()],
                ['loc' => route('blogs.urdu.index'), 'lastmod' => now()],
                ['loc' => route('blogs.english.index'), 'lastmod' => now()],
                ['loc' => route('videos.index'), 'lastmod' => now()],
                ['loc' => route('magazines.index'), 'lastmod' => now()],
                ['loc' => route('about'), 'lastmod' => now()],
                ['loc' => route('contact.create'), 'lastmod' => now()],
                ['loc' => route('policies.privacy'), 'lastmod' => now()],
                ['loc' => route('policies.terms'), 'lastmod' => now()],
                ['loc' => route('policies.shipping'), 'lastmod' => now()],
                ['loc' => route('policies.refund'), 'lastmod' => now()],
            ]);

            Product::active()->each(fn ($item) => $urls->push(['loc' => route('products.show', $item), 'lastmod' => $item->updated_at]));
            Blog::published()->each(fn ($item) => $urls->push([
                'loc' => route($item->language === 'ur' ? 'blogs.urdu.show' : 'blogs.english.show', $item->slug),
                'lastmod' => $item->updated_at,
            ]));
            Video::published()->each(fn ($item) => $urls->push(['loc' => route('videos.show', $item), 'lastmod' => $item->updated_at]));
            Magazine::active()->each(fn ($item) => $urls->push(['loc' => route('magazines.show', $item), 'lastmod' => $item->updated_at]));

            return view('seo.sitemap', compact('urls'))->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        return response("User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /checkout\nSitemap: ".route('sitemap')."\n")
            ->header('Content-Type', 'text/plain');
    }
}
