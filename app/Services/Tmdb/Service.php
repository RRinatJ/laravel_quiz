<?php

declare(strict_types=1);

namespace App\Services\Tmdb;

use Illuminate\Support\Facades\Storage;

final class Service
{
    public function saveTmdbImage(string $tmdb_image, string $directory = 'images'): string
    {
        $filePath = $directory.'/'.basename($tmdb_image);

        if (Storage::disk('public')->put($filePath, file_get_contents($tmdb_image))) {
            return $filePath;
        }

        return '';
    }
}
