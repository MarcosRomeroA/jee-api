<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindTournamentBackgroundImageQuery implements Query
{
    public function __construct(
        public string $tournamentId,
    ) {
    }
}
