<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Service\Image;

use App\Contexts\Web\User\Domain\Exception\InvalidProfileImageException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Psr\Log\LoggerInterface;

final readonly class ProfileImageOptimizer
{
    private const array ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const int MAX_FILE_SIZE_BYTES = 5 * 1024 * 1024; // 5MB
    private const int WEBP_QUALITY = 85;

    private const int SIZE_MAIN = 512;
    private const int SIZE_MEDIUM = 128;
    private const int SIZE_SMALL = 64;

    private ImageManager $manager;

    public function __construct(
        private LoggerInterface $logger,
    ) {
        $this->manager = new ImageManager(new Driver());
    }

    public function optimize(string $imagePath): ProfileImageResult
    {
        $this->validateImage($imagePath);

        $originalSizeKb = (int) ceil(filesize($imagePath) / 1024);
        $this->logger->info('Profile image optimization started', [
            'original_size_kb' => $originalSizeKb,
            'path' => $imagePath,
        ]);

        $image = $this->manager->read($imagePath);

        $mainImage = $this->createOptimizedVersion($image, self::SIZE_MAIN);
        $mediumImage = $this->createOptimizedVersion($image, self::SIZE_MEDIUM);
        $smallImage = $this->createOptimizedVersion($image, self::SIZE_SMALL);

        $mainSizeKb = (int) ceil(strlen($mainImage) / 1024);
        $mediumSizeKb = (int) ceil(strlen($mediumImage) / 1024);
        $smallSizeKb = (int) ceil(strlen($smallImage) / 1024);

        $this->logger->info('Profile image optimization completed', [
            'original_size_kb' => $originalSizeKb,
            'main_size_kb' => $mainSizeKb,
            'medium_size_kb' => $mediumSizeKb,
            'small_size_kb' => $smallSizeKb,
        ]);

        return new ProfileImageResult(
            mainImage: $mainImage,
            mediumThumbnail: $mediumImage,
            smallThumbnail: $smallImage,
            originalSizeKb: $originalSizeKb,
            mainSizeKb: $mainSizeKb,
            mediumSizeKb: $mediumSizeKb,
            smallSizeKb: $smallSizeKb,
        );
    }

    private function validateImage(string $imagePath): void
    {
        if (!file_exists($imagePath)) {
            throw new InvalidProfileImageException('File not found');
        }

        $fileSize = filesize($imagePath);
        if ($fileSize === false || $fileSize > self::MAX_FILE_SIZE_BYTES) {
            throw new InvalidProfileImageException('File size exceeds 5MB limit');
        }

        $mimeType = mime_content_type($imagePath);
        if ($mimeType === false || !in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new InvalidProfileImageException(
                'Invalid file type. Allowed: jpg, png, webp'
            );
        }
    }

    private function createOptimizedVersion(ImageInterface $image, int $size): string
    {
        $clone = clone $image;

        // Cover resize: redimensiona y recorta al centro para obtener un cuadrado
        $clone->cover($size, $size);

        return $clone->toWebp(self::WEBP_QUALITY)->toString();
    }
}
