<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Dashboard\Application\GetStats;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class DashboardStatsResponse extends Response
{
    public function __construct(
        private readonly int $usersCount,
        private readonly int $postsCount,
        private readonly int $teamsCount,
        private readonly int $tournamentsCount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'users' => $this->usersCount,
            'posts' => $this->postsCount,
            'teams' => $this->teamsCount,
            'tournaments' => $this->tournamentsCount,
        ];
    }
}
