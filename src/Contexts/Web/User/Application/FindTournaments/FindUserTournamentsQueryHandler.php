<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FindUserTournamentsQueryHandler implements QueryHandler
{
    public function __construct(
        private UserTournamentsFinder $finder
    ) {
    }

    public function __invoke(FindUserTournamentsQuery $query): array
    {
        $userId = new Uuid($query->userId);

        return ($this->finder)($userId);
    }
}
