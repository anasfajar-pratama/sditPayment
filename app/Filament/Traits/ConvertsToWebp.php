<?php

namespace App\Filament\Traits;

use Illuminate\Support\Facades\Storage;

trait ConvertsToWebp
{
    public function convertToWebp(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return $path;
        }

        try {
            $fileContent = $disk->get($path);
            $img = @imagecreatefromstring($fileContent);

            if ($img === false) {
                return $path;
            }

            $dir = dirname($path);
            $originalFile = basename($path);
            $filename = pathinfo($originalFile, PATHINFO_FILENAME) . '.webp';
            $webpPath = ($dir !== '.' ? $dir . '/' : '') . $filename;

            ob_start();
            imagewebp($img, null, 80);
            $webpContent = ob_get_clean();
            imagedestroy($img);

            $disk->put($webpPath, $webpContent);
            $disk->delete($path);

            return $webpPath;
        } catch (\Exception $e) {
            return $path;
        }
    }
}
