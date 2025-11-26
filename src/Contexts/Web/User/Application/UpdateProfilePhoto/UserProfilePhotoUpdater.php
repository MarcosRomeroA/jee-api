<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateProfilePhoto;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\ProfileImageValue;
use FilesystemIterator;
use Symfony\Component\Filesystem\Filesystem;

final readonly class UserProfilePhotoUpdater
{
    public function __construct(
        private FileManager $fileManager,
        private UserRepository $repository,
    ) {
    }

    public function __invoke(Uuid $userId, string $imagePath, string $filename): void
    {
        $tempFile = $imagePath . '/' . $filename;
        $filesystem = new Filesystem();

        try {
            $this->fileManager->upload($imagePath, 'user/profile', $filename);
            $user = $this->repository->findById($userId);
            $user->setProfileImage(new ProfileImageValue($filename));
            $this->repository->save($user);

            // Delete temp file after successful upload
            if ($filesystem->exists($tempFile)) {
                $filesystem->remove($tempFile);
            }

            // Clean up empty directories
            $this->removeEmptyDirectories($imagePath, $filesystem);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Recursively remove empty directories up to the resource base folder
     */
    private function removeEmptyDirectories(string $directory, Filesystem $filesystem): void
    {
        if (!$filesystem->exists($directory) || !is_dir($directory)) {
            return;
        }

        // Check if directory is empty
        $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);
        if (!$iterator->valid()) {
            $parentDir = dirname($directory);
            $filesystem->remove($directory);

            // Continue cleaning parent directories if they're empty
            // Stop at /var/tmp/resource to avoid deleting the base folder
            if (str_contains($parentDir, '/var/tmp/resource') && $parentDir !== '/var/tmp/resource') {
                $this->removeEmptyDirectories($parentDir, $filesystem);
            }
        }
    }
}
