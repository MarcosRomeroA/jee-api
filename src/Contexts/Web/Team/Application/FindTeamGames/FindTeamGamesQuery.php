<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindTeamGames;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindTeamGamesQuery implements Query
{
    public function __construct(
        public string $teamId
    ) {
    }
}
