<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Domain\FileManager;

interface ImageUploader
{
    /**
     * Uploads a base64 encoded image to storage.
     *
     * @param string $base64Image The base64 encoded image (with data:image/xxx;base64, prefix)
     * @param string $context The storage context/path (e.g., 'team/uuid', 'tournament/uuid')
     * @return string The generated filename
     */
    public function upload(string $base64Image, string $context): string;

    /**
     * Checks if a string is a valid base64 image.
     */
    public function isBase64Image(?string $value): bool;
}
