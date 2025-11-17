<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\SocialNetworkCode;

interface SocialNetworkRepository
{
    public function findAll(): array;

    public function findById(Uuid $id): ?SocialNetwork;

    public function findByCode(SocialNetworkCode $code): ?SocialNetwork;

    /**
     * Find all social networks except those already added by the user
     *
     * @return SocialNetwork[]
     */
    public function findAvailableForUser(User $user): array;
}
