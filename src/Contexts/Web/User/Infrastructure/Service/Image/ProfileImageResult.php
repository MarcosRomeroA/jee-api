<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Service\Image;

final readonly class ProfileImageResult
{
    public function __construct(
        public string $mainImage,
        public string $mediumThumbnail,
        public string $smallThumbnail,
        public int $originalSizeKb,
        public int $mainSizeKb,
        public int $mediumSizeKb,
        public int $smallSizeKb,
    ) {
    }

    public function getMainFilename(string $baseFilename): string
    {
        return $this->replaceExtension($baseFilename, 'webp');
    }

    public function getMediumFilename(string $baseFilename): string
    {
        return $this->replaceExtension($baseFilename, 'webp', '_128');
    }

    public function getSmallFilename(string $baseFilename): string
    {
        return $this->replaceExtension($baseFilename, 'webp', '_64');
    }

    private function replaceExtension(string $filename, string $extension, string $suffix = ''): string
    {
        $pathInfo = pathinfo($filename);
        return $pathInfo['filename'] . $suffix . '.' . $extension;
    }
}
