<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindUserTournamentsQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}

