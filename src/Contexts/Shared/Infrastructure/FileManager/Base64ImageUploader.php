<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\FileManager;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Infrastructure\Image\ImageOptimizer;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final readonly class Base64ImageUploader implements ImageUploader
{
    public function __construct(
        private FilesystemOperator $defaultStorage,
        private ImageOptimizer $imageOptimizer,
        private LoggerInterface $logger,
        private KernelInterface $kernel,
    ) {
    }

    /**
     * Uploads a base64 encoded image to storage, optimized to WebP format.
     *
     * @param string $base64Image The base64 encoded image (with data:image/xxx;base64, prefix)
     * @param string $context The storage context/path (e.g., 'team/uuid', 'tournament/uuid')
     * @return string The generated filename (always .webp)
     * @throws \InvalidArgumentException If the base64 format is invalid
     */
    public function upload(string $base64Image, string $context): string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            throw new \InvalidArgumentException('Invalid base64 image format');
        }

        $imageType = $matches[1];
        $base64Data = substr($base64Image, strpos($base64Image, ',') + 1);
        $imageData = base64_decode($base64Data);

        if ($imageData === false) {
            throw new \InvalidArgumentException('Failed to decode base64 image');
        }

        $tempFilename = uniqid() . '.' . $imageType;
        $tempDir = $this->kernel->getProjectDir() . '/var/tmp/images/' . $context;
        $filesystem = new Filesystem();

        if (!$filesystem->exists($tempDir)) {
            $filesystem->mkdir($tempDir, 0755);
        }

        $tempFilePath = $tempDir . '/' . $tempFilename;

        if (file_put_contents($tempFilePath, $imageData) === false) {
            throw new \RuntimeException('Failed to write temporary image file');
        }

        try {
            // Optimize image to WebP
            $result = $this->imageOptimizer->optimize($tempFilePath);
            $webpFilename = $result->getFilename($tempFilename);
            $path = 'jee/' . $context . '/' . $webpFilename;

            $this->defaultStorage->write($path, $result->imageData, [
                'ContentType' => 'image/webp',
                'CacheControl' => 'public, max-age=31536000',
            ]);

            $this->logger->info('Image uploaded to storage', [
                'path' => $path,
                'original_size_kb' => $result->originalSizeKb,
                'optimized_size_kb' => $result->optimizedSizeKb,
            ]);

            return $webpFilename;
        } finally {
            if ($filesystem->exists($tempDir)) {
                $filesystem->remove($tempDir);
            }
        }
    }

    /**
     * Checks if a string is a valid base64 image.
     */
    public function isBase64Image(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return (bool) preg_match('/^data:image\/\w+;base64,/', $value);
    }
}
