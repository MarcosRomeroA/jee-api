<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Image;

final readonly class ImageOptimizationResult
{
    public function __construct(
        public string $imageData,
        public int $originalSizeKb,
        public int $optimizedSizeKb,
    ) {
    }

    public function getFilename(string $baseFilename): string
    {
        $pathInfo = pathinfo($baseFilename);
        return $pathInfo['filename'] . '.webp';
    }
}
