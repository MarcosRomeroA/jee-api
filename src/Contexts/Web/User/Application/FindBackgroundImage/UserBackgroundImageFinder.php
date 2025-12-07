<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserBackgroundImageFinder
{
    public function __construct(
        private UserRepository $repository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(Uuid $userId): BackgroundImageResponse
    {
        $user = $this->repository->findById($userId);

        $backgroundImageUrl = $user->getBackgroundImageUrl($this->cdnBaseUrl);

        return new BackgroundImageResponse($backgroundImageUrl);
    }
}
