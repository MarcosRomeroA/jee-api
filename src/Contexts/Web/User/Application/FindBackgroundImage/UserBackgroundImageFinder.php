<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserBackgroundImageFinder
{
    public function __construct(
        private UserRepository $repository,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(Uuid $userId): BackgroundImageResponse
    {
        $user = $this->repository->findById($userId);

        $backgroundImageUrl = null;
        $backgroundImageFilename = $user->getBackgroundImage()->value();

        if ($backgroundImageFilename !== '') {
            $backgroundImageUrl = $this->fileManager->generateTemporaryUrl(
                'user/' . $userId->value() . '/background',
                $backgroundImageFilename
            );
        }

        return new BackgroundImageResponse($backgroundImageUrl);
    }
}
