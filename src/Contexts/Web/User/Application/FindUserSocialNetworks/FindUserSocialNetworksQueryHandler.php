<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindUserSocialNetworks;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FindUserSocialNetworksQueryHandler implements QueryHandler
{
    public function __construct(
        private UserSocialNetworksFinder $finder
    ) {
    }

    public function __invoke(FindUserSocialNetworksQuery $query): Response
    {
        $userId = new Uuid($query->userId());

        return ($this->finder)($userId);
    }
}
