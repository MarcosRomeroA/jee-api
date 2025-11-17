<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\SearchSocialNetworks;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final class SearchSocialNetworksQuery implements Query
{
    public function __construct(
        private readonly string $userId,
        private readonly bool $mine
    ) {
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function mine(): bool
    {
        return $this->mine;
    }
}
