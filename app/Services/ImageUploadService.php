<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /** Allowed mime types for uploads. */
    protected array $allowedMimes = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
    ];

    /** Max size in bytes (20 MB). */
    protected int $maxSize = 20 * 1024 * 1024;

    /** Allowed sub-folders inside public_html/uploads. */
    protected array $folders = [
        'products', 'categories', 'subcategories', 'blog', 'news',
        'certificates', 'partners', 'portfolio', 'testimonials', 'misc',
    ];

    /**
     * Store an uploaded file into public_html/uploads/<folder>/.
     * Returns the public relative path, e.g. /uploads/products/uuid.jpg
     */
    public function store(UploadedFile $file, string $folder = 'misc'): string
    {
        if (! in_array($folder, $this->folders, true)) {
            $folder = 'misc';
        }

        if (! $file->isValid()) {
            throw new \RuntimeException('Загруженный файл повреждён.');
        }

        if ($file->getSize() > $this->maxSize) {
            throw new \RuntimeException('Файл слишком большой (макс. 20 МБ).');
        }

        $mime = $file->getMimeType();
        if (! in_array($mime, $this->allowedMimes, true)) {
            throw new \RuntimeException('Недопустимый тип файла. Разрешены: jpg, png, webp, gif.');
        }

        $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $name = Str::uuid()->toString() . '.' . $ext;

        // Saved relative to the 'public_html' disk root (../public_html/uploads).
        Storage::disk('public_html')->putFileAs($folder, $file, $name);

        return '/uploads/' . $folder . '/' . $name;
    }

    /**
     * Resolve image input: either an uploaded file ('file' key) or a plain URL string.
     * Returns the stored/normalized path or null.
     */
    public function resolve($fileOrUrl, ?string $imageUrl, string $folder = 'misc'): ?string
    {
        if ($fileOrUrl instanceof UploadedFile) {
            return $this->store($fileOrUrl, $folder);
        }
        if (is_string($imageUrl) && trim($imageUrl) !== '') {
            return trim($imageUrl);
        }
        return null;
    }

    /**
     * Delete a physical file by its public relative path (/uploads/...).
     * External URLs (http...) are ignored.
     */
    public function deleteByRelativePath(?string $path): void
    {
        if (! is_string($path) || trim($path) === '') {
            return;
        }
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return; // external URL, not ours
        }

        // Normalize: strip leading /uploads/
        $relative = ltrim($path, '/');
        if (Str::startsWith($relative, 'uploads/')) {
            $relative = Str::after($relative, 'uploads/');
        }

        try {
            if (Storage::disk('public_html')->exists($relative)) {
                Storage::disk('public_html')->delete($relative);
            }
        } catch (\Throwable $e) {
            // swallow — file cleanup is best-effort
        }
    }
}
