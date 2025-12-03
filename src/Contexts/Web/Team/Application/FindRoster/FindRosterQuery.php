<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindRoster;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindRosterQuery implements Query
{
    public function __construct(
        public string $rosterId,
    ) {
    }
}

