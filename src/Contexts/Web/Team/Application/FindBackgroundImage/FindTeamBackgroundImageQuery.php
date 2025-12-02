<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindTeamBackgroundImageQuery implements Query
{
    public function __construct(
        public string $teamId,
    ) {
    }
}
