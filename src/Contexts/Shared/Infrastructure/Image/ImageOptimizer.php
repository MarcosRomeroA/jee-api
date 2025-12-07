<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Image;

use App\Contexts\Shared\Domain\Exception\InvalidImageException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Psr\Log\LoggerInterface;

final readonly class ImageOptimizer
{
    private const array ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const int MAX_FILE_SIZE_BYTES = 5 * 1024 * 1024; // 5MB
    private const int WEBP_QUALITY = 85;
    private const int MAX_DIMENSION = 1920;

    private ImageManager $manager;

    public function __construct(
        private LoggerInterface $logger,
    ) {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimizes an image to WebP format, scaling down if larger than max dimension.
     *
     * @param string $imagePath Path to the image file
     * @return ImageOptimizationResult The optimized image data and metadata
     */
    public function optimize(string $imagePath): ImageOptimizationResult
    {
        $this->validateImage($imagePath);

        $originalSizeKb = (int) ceil(filesize($imagePath) / 1024);
        $this->logger->info('Image optimization started', [
            'original_size_kb' => $originalSizeKb,
            'path' => $imagePath,
        ]);

        $image = $this->manager->read($imagePath);

        $width = $image->width();
        $height = $image->height();

        // Scale down if larger than max dimension while maintaining aspect ratio
        if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
            $image->scaleDown(self::MAX_DIMENSION, self::MAX_DIMENSION);
        }

        $optimizedImage = $image->toWebp(self::WEBP_QUALITY)->toString();
        $optimizedSizeKb = (int) ceil(strlen($optimizedImage) / 1024);

        $this->logger->info('Image optimization completed', [
            'original_size_kb' => $originalSizeKb,
            'optimized_size_kb' => $optimizedSizeKb,
            'reduction_percent' => $originalSizeKb > 0
                ? round((1 - $optimizedSizeKb / $originalSizeKb) * 100, 1)
                : 0,
        ]);

        return new ImageOptimizationResult(
            imageData: $optimizedImage,
            originalSizeKb: $originalSizeKb,
            optimizedSizeKb: $optimizedSizeKb,
        );
    }

    private function validateImage(string $imagePath): void
    {
        if (!file_exists($imagePath)) {
            throw new InvalidImageException('File not found');
        }

        $fileSize = filesize($imagePath);
        if ($fileSize === false || $fileSize > self::MAX_FILE_SIZE_BYTES) {
            throw new InvalidImageException('File size exceeds 5MB limit');
        }

        $mimeType = mime_content_type($imagePath);
        if ($mimeType === false || !in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new InvalidImageException(
                'Invalid file type. Allowed: jpg, png, webp'
            );
        }
    }
}
