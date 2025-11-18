<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\SearchSocialNetworks;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Shared\SocialNetworkCollectionResponse;
use App\Contexts\Web\User\Application\Shared\UserSocialNetworkCollectionResponse;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\SocialNetworkRepository;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\UserSocialNetworkRepository;

final readonly class SocialNetworkSearcher
{
    public function __construct(
        private UserRepository $userRepository,
        private SocialNetworkRepository $socialNetworkRepository,
        private UserSocialNetworkRepository $userSocialNetworkRepository
    ) {
    }

    public function __invoke(Uuid $userId, bool $available): Response
    {
        if ($available) {
            $user = $this->findUser($userId);
            return $this->findAvailableSocialNetworks($user);
        }

        return $this->findAllSocialNetworks();
    }

    private function findUser(Uuid $userId): User
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new UserNotFoundException($userId->value());
        }

        return $user;
    }

    private function findAvailableSocialNetworks(User $user): SocialNetworkCollectionResponse
    {
        $socialNetworks = $this->socialNetworkRepository->findAvailableForUser($user);
        return new SocialNetworkCollectionResponse($socialNetworks);
    }

    private function findAllSocialNetworks(): SocialNetworkCollectionResponse
    {
        $socialNetworks = $this->socialNetworkRepository->findAll();
        return new SocialNetworkCollectionResponse($socialNetworks);
    }
}
