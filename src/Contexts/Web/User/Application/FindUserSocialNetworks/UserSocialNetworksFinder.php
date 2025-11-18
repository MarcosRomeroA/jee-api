<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindUserSocialNetworks;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\UserSocialNetworkCollectionResponse;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\UserSocialNetworkRepository;

final readonly class UserSocialNetworksFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private UserSocialNetworkRepository $userSocialNetworkRepository
    ) {
    }

    public function __invoke(Uuid $userId): Response
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new UserNotFoundException($userId->value());
        }

        $userSocialNetworks = $this->userSocialNetworkRepository->findByUser($user);

        return new UserSocialNetworkCollectionResponse($userSocialNetworks);
    }
}
