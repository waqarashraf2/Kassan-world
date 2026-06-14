<?php

namespace App\Support;

use Illuminate\Support\Str;

class FrontendAssets
{
    public function cssUrl(): ?string
    {
        return $this->assetUrl('resources/css/app.css', 'css');
    }

    public function jsUrl(): ?string
    {
        return $this->assetUrl('resources/js/app.js', 'js');
    }

    private function assetUrl(string $entry, string $extension): ?string
    {
        $manifestPath = public_path('build/manifest.json');

        if (is_file($manifestPath)) {
            $manifest = json_decode((string) file_get_contents($manifestPath), true);

            if (is_array($manifest)) {
                $chunk = $manifest[$entry] ?? collect($manifest)->first(
                    fn ($value, $key) => Str::of((string) $key)
                        ->replace('\\', '/')
                        ->endsWith('/'.$entry)
                );

                if (is_array($chunk) && isset($chunk['file'])) {
                    $file = public_path('build/'.ltrim($chunk['file'], '/'));
                    if (is_file($file)) {
                        return asset('build/'.ltrim($chunk['file'], '/'));
                    }
                }
            }
        }

        $matches = glob(public_path("build/assets/app-*.{$extension}")) ?: [];
        usort($matches, fn (string $left, string $right) => filemtime($right) <=> filemtime($left));

        return isset($matches[0])
            ? asset('build/assets/'.basename($matches[0]))
            : null;
    }
}
