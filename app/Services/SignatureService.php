<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureService
{
    private const DISK = 'local';

    private const DIRECTORY = 'signatures';

    /**
     * Persist a signature to storage and return the stored path.
     * Accepts raw SVG text, a base64 data URI (PNG/JPEG), or an existing stored path.
     */
    public function store(string $base64OrPath): string
    {
        if ($this->isStoredPath($base64OrPath)) {
            return $base64OrPath;
        }

        $trimmed = ltrim($base64OrPath);
        if (str_starts_with($trimmed, '<svg') || str_starts_with($trimmed, '<?xml')) {
            $path = self::DIRECTORY.'/'.Str::uuid().'.svg';
            Storage::disk(self::DISK)->put($path, $base64OrPath);
        } else {
            $path = self::DIRECTORY.'/'.Str::uuid().'.png';
            Storage::disk(self::DISK)->put($path, $this->extractImageData($base64OrPath));
        }

        return $path;
    }

    /**
     * Return the data URI for a given stored path or raw base64/SVG.
     * Returns null if the file no longer exists.
     */
    public function toBase64(string $pathOrBase64): ?string
    {
        if (! $this->isStoredPath($pathOrBase64)) {
            $trimmed = ltrim($pathOrBase64);
            if (str_starts_with($trimmed, '<svg') || str_starts_with($trimmed, '<?xml')) {
                return 'data:image/svg+xml;base64,'.base64_encode($pathOrBase64);
            }

            return $pathOrBase64;
        }

        if (! Storage::disk(self::DISK)->exists($pathOrBase64)) {
            return null;
        }

        $data = Storage::disk(self::DISK)->get($pathOrBase64);

        if (str_ends_with($pathOrBase64, '.svg')) {
            return 'data:image/svg+xml;base64,'.base64_encode($data);
        }

        $mime = str_starts_with($data, "\xff\xd8\xff") ? 'image/jpeg' : 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($data);
    }

    /**
     * Delete a stored signature file.
     */
    public function delete(string $path): void
    {
        if ($this->isStoredPath($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    /**
     * Determine whether a value is a stored path (not raw base64).
     */
    public function isStoredPath(string $value): bool
    {
        return str_starts_with($value, self::DIRECTORY.'/');
    }

    private function extractImageData(string $base64): string
    {
        if (str_contains($base64, ',')) {
            [, $base64] = explode(',', $base64, 2);
        }

        return base64_decode($base64);
    }
}
