<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\RemoveUserSocialNetwork;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\Exception\SocialNetworkNotFoundException;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\Exception\UserSocialNetworkNotFoundException;
use App\Contexts\Web\User\Domain\SocialNetwork;
use App\Contexts\Web\User\Domain\SocialNetworkRepository;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\UserSocialNetwork;
use App\Contexts\Web\User\Domain\UserSocialNetworkRepository;

final readonly class UserSocialNetworkRemover
{
    public function __construct(
        private UserRepository $userRepository,
        private SocialNetworkRepository $socialNetworkRepository,
        private UserSocialNetworkRepository $userSocialNetworkRepository
    ) {
    }

    public function __invoke(Uuid $userId, Uuid $socialNetworkId): void
    {
        $user = $this->findUser($userId);
        $socialNetwork = $this->findSocialNetwork($socialNetworkId);
        $userSocialNetwork = $this->findUserSocialNetwork($user, $socialNetwork);

        $userSocialNetwork->delete();
        $this->userSocialNetworkRepository->save($userSocialNetwork);
    }

    private function findUser(Uuid $userId): User
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new UserNotFoundException($userId->value());
        }

        return $user;
    }

    private function findSocialNetwork(Uuid $socialNetworkId): SocialNetwork
    {
        $socialNetwork = $this->socialNetworkRepository->findById($socialNetworkId);

        if ($socialNetwork === null) {
            throw new SocialNetworkNotFoundException($socialNetworkId->value());
        }

        return $socialNetwork;
    }

    private function findUserSocialNetwork(User $user, SocialNetwork $socialNetwork): UserSocialNetwork
    {
        $userSocialNetwork = $this->userSocialNetworkRepository->findByUserAndSocialNetwork($user, $socialNetwork);

        if ($userSocialNetwork === null) {
            throw new UserSocialNetworkNotFoundException($socialNetwork->name()->value());
        }

        return $userSocialNetwork;
    }
}
