<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class VerifyPlayerRankCommand implements Command
{
    public function __construct(
        public string $playerId,
    ) {
    }
}
