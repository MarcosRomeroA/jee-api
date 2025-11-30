<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\FindRequirements;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindGameRequirementsQuery implements Query
{
    public function __construct(
        public string $gameId,
    ) {
    }
}
