<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateProfilePhoto;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\ProfileImageValue;

final readonly class UserProfilePhotoUpdater
{
    public function __construct(
        private FileManager $fileManager,
        private UserRepository $repository,
    )
    {
    }

    public function __invoke(Uuid $userId, string $imagePath, string $filename): void
    {
        $this->fileManager->upload($imagePath, 'user/profile', $filename);
        $user = $this->repository->findById($userId);
        $user->setProfileImage(new ProfileImageValue($filename));
        $this->repository->save($user);
    }
}