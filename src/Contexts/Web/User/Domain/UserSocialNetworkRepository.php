<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface UserSocialNetworkRepository
{
    public function save(UserSocialNetwork $userSocialNetwork): void;

    public function findById(Uuid $id): ?UserSocialNetwork;

    /**
     * Find user's social network by user and social network ID (excluding deleted)
     */
    public function findByUserAndSocialNetwork(User $user, SocialNetwork $socialNetwork): ?UserSocialNetwork;

    /**
     * Find user's social network by user and social network ID (including deleted)
     */
    public function findByUserAndSocialNetworkIncludingDeleted(User $user, SocialNetwork $socialNetwork): ?UserSocialNetwork;

    /**
     * Find all social networks for a user (excluding deleted)
     *
     * @return UserSocialNetwork[]
     */
    public function findByUser(User $user): array;

    public function delete(UserSocialNetwork $userSocialNetwork): void;
}
