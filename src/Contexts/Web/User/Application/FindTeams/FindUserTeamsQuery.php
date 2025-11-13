<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindTeams;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindUserTeamsQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}

