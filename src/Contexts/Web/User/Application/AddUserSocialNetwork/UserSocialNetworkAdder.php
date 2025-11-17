<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\AddUserSocialNetwork;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\Exception\SocialNetworkNotFoundException;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\SocialNetwork;
use App\Contexts\Web\User\Domain\SocialNetworkRepository;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\UserSocialNetwork;
use App\Contexts\Web\User\Domain\UserSocialNetworkRepository;
use App\Contexts\Web\User\Domain\ValueObject\SocialNetworkUsername;

final readonly class UserSocialNetworkAdder
{
    public function __construct(
        private UserRepository $userRepository,
        private SocialNetworkRepository $socialNetworkRepository,
        private UserSocialNetworkRepository $userSocialNetworkRepository
    ) {
    }

    public function __invoke(
        Uuid $userId,
        Uuid $socialNetworkId,
        SocialNetworkUsername $username
    ): void {
        $user = $this->findUser($userId);
        $socialNetwork = $this->findSocialNetwork($socialNetworkId);

        // Search including soft-deleted records to avoid unique constraint violation
        $existing = $this->userSocialNetworkRepository->findByUserAndSocialNetworkIncludingDeleted($user, $socialNetwork);

        if ($existing !== null) {
            // Update existing username and restore if soft-deleted (upsert)
            $existing->updateUsername($username);
            $existing->restore();
            $this->userSocialNetworkRepository->save($existing);
        } else {
            // Create new entry
            $userSocialNetwork = new UserSocialNetwork(
                Uuid::random(),
                $user,
                $socialNetwork,
                $username
            );
            $this->userSocialNetworkRepository->save($userSocialNetwork);
        }
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
}
