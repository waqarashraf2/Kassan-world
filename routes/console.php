<?php

use Illuminate\Foundation\Inspiring;
use App\Models\Magazine;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('magazines:sync-public-covers', function () {
    $projectRoot = dirname(__DIR__);
    $source = $projectRoot.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'magazines';
    $target = $projectRoot.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'magazines';

    File::ensureDirectoryExists($target, 0755, true);

    if (! File::isDirectory($source)) {
        $this->warn("Source directory not found: {$source}");
        $this->info("Target directory ready: {$target}");

        return 0;
    }

    $copied = 0;
    $skipped = 0;
    $normalized = 0;

    foreach (File::files($source) as $file) {
        $targetPath = $target.DIRECTORY_SEPARATOR.$file->getFilename();

        if (File::exists($targetPath)) {
            $skipped++;
            continue;
        }

        File::copy($file->getPathname(), $targetPath);
        $copied++;
    }

    Magazine::query()
        ->whereNotNull('cover_image')
        ->get()
        ->each(function (Magazine $magazine) use (&$normalized): void {
            $path = str_replace('\\', '/', trim($magazine->cover_image));
            $path = parse_url($path, PHP_URL_PATH) ?: $path;
            $path = ltrim($path, '/');

            foreach ([
                'public/storage/uploads/magazines/' => 'storage/uploads/magazines/',
                'storage/app/public/uploads/magazines/' => 'storage/uploads/magazines/',
                'app/public/uploads/magazines/' => 'storage/uploads/magazines/',
                'uploads/magazines/' => 'storage/uploads/magazines/',
            ] as $prefix => $replacement) {
                if (! Str::startsWith($path, $prefix)) {
                    continue;
                }

                $path = $replacement.Str::after($path, $prefix);
                break;
            }

            if ($path === $magazine->cover_image) {
                return;
            }

            $magazine->forceFill(['cover_image' => $path])->save();
            $normalized++;
        });

    $this->info("Magazine covers copied: {$copied}");
    $this->info("Already existed, skipped: {$skipped}");
    $this->info("Database paths normalized: {$normalized}");
    $this->info("Public folder: {$target}");

    return 0;
})->purpose('Copy existing magazine cover uploads into public/storage without deleting originals');
