<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\SearchSocialNetworks;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class SearchSocialNetworksQueryHandler implements QueryHandler
{
    public function __construct(
        private SocialNetworkSearcher $searcher
    ) {
    }

    public function __invoke(SearchSocialNetworksQuery $query): Response
    {
        $userId = new Uuid($query->userId());

        return ($this->searcher)($userId, $query->mine());
    }
}
