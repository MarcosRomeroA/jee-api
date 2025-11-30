<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\FindRequirements;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FindGameRequirementsQueryHandler implements QueryHandler
{
    public function __construct(
        private GameRequirementsFinder $finder,
    ) {
    }

    public function __invoke(FindGameRequirementsQuery $query): GameRequirementsResponse
    {
        return $this->finder->__invoke(
            new Uuid($query->gameId),
        );
    }
}
