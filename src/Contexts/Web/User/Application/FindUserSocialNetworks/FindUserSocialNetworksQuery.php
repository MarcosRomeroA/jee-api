<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindUserSocialNetworks;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindUserSocialNetworksQuery implements Query
{
    public function __construct(
        private string $userId
    ) {
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
