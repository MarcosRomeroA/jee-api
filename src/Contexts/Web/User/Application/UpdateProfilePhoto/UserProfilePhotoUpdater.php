<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateProfilePhoto;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UserProfilePhotoUpdater
{
    public function __construct(
        private FileManager $fileManager
    )
    {
    }

    public function __invoke(Uuid $id, string $imagePath): void
    {
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $filename = uniqid().'.'.$extension;
        $this->fileManager->upload($imagePath, 'user/profile', $filename);
    }
}