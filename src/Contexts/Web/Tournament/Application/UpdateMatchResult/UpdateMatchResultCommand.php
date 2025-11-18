<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\UpdateMatchResult;

use App\Contexts\Shared\Domain\Bus\Command\Command;

final readonly class UpdateMatchResultCommand implements Command
{
    /**
     * @param array<string, int> $scores
     */
    public function __construct(
        public string $matchId,
        public array $scores,
        public ?string $winnerId = null
    ) {
    }
}
