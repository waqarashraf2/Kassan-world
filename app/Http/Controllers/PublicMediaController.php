<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicMediaController extends Controller
{
    public function __invoke(string $path): StreamedResponse
    {
        abort_if(
            ! Str::startsWith($path, 'uploads/')
            || Str::contains($path, ['..', '\\', "\0"]),
            404
        );

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        return $disk->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
