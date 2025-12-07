<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Service\Image;

use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;

final readonly class ProfileImageUploader
{
    private const string AVATAR_FILENAME = 'avatar.webp';
    private const string AVATAR_MEDIUM_FILENAME = 'avatar_128.webp';
    private const string AVATAR_SMALL_FILENAME = 'avatar_64.webp';

    public function __construct(
        private FilesystemOperator $defaultStorage,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Uploads profile images to R2 with fixed filenames per user.
     * Structure: user/profile/{userId}/avatar.webp, avatar_128.webp, avatar_64.webp
     *
     * @param ProfileImageResult $result The optimized image data
     * @param string $userId The user's UUID
     */
    public function upload(ProfileImageResult $result, string $userId): void
    {
        $basePath = $this->getUserAvatarPath($userId);

        $this->uploadImage($result->mainImage, $basePath . '/' . self::AVATAR_FILENAME);
        $this->uploadImage($result->mediumThumbnail, $basePath . '/' . self::AVATAR_MEDIUM_FILENAME);
        $this->uploadImage($result->smallThumbnail, $basePath . '/' . self::AVATAR_SMALL_FILENAME);

        $this->logger->info('Profile images uploaded to R2', [
            'userId' => $userId,
            'main' => $basePath . '/' . self::AVATAR_FILENAME,
            'medium' => $basePath . '/' . self::AVATAR_MEDIUM_FILENAME,
            'small' => $basePath . '/' . self::AVATAR_SMALL_FILENAME,
            'main_size_kb' => $result->mainSizeKb,
            'medium_size_kb' => $result->mediumSizeKb,
            'small_size_kb' => $result->smallSizeKb,
        ]);
    }

    /**
     * Gets the base path for a user's avatar files.
     * Note: The 'jee/' prefix is required because files in R2 bucket are stored under this prefix.
     */
    public function getUserAvatarPath(string $userId): string
    {
        return 'jee/user/profile/' . $userId;
    }

    private function uploadImage(string $content, string $path): void
    {
        $this->defaultStorage->write($path, $content, [
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000',
        ]);
    }
}
