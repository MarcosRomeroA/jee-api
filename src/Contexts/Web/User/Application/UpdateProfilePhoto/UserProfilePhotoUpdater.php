<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateProfilePhoto;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Infrastructure\Service\Image\ProfileImageOptimizer;
use App\Contexts\Web\User\Infrastructure\Service\Image\ProfileImageUploader;
use FilesystemIterator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

final readonly class UserProfilePhotoUpdater
{
    public function __construct(
        private ProfileImageOptimizer $optimizer,
        private ProfileImageUploader $uploader,
        private UserRepository $repository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Uuid $userId, string $imagePath, string $filename): void
    {
        $tempFile = $imagePath . '/' . $filename;
        $filesystem = new Filesystem();

        try {
            // Optimize image: resize, crop, convert to WebP
            $this->logger->info('Starting profile photo optimization', [
                'userId' => $userId->value(),
                'tempFile' => $tempFile,
            ]);
            $result = $this->optimizer->optimize($tempFile);

            // Upload all versions to R2 with fixed filenames per user
            $this->logger->info('Uploading profile photo to R2', [
                'userId' => $userId->value(),
            ]);
            $this->uploader->upload($result, $userId->value());

            // Update user avatar timestamp for cache busting
            $user = $this->repository->findById($userId);
            $user->updateAvatar();
            $this->repository->save($user);

            $this->logger->info('Profile photo updated successfully', [
                'userId' => $userId->value(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to update profile photo', [
                'userId' => $userId->value(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        // Delete temp file after successful upload
        if ($filesystem->exists($tempFile)) {
            $filesystem->remove($tempFile);
        }

        // Clean up empty directories
        $this->removeEmptyDirectories($imagePath, $filesystem);
    }

    private function removeEmptyDirectories(string $directory, Filesystem $filesystem): void
    {
        if (!$filesystem->exists($directory) || !is_dir($directory)) {
            return;
        }

        $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);
        if (!$iterator->valid()) {
            $parentDir = dirname($directory);
            $filesystem->remove($directory);

            if (str_contains($parentDir, '/var/tmp/resource') && $parentDir !== '/var/tmp/resource') {
                $this->removeEmptyDirectories($parentDir, $filesystem);
            }
        }
    }
}
