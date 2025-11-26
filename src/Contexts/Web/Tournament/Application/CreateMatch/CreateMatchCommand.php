<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\CreateMatch;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreateMatchCommand implements Command
{
    /**
     * @param array<string> $teamIds
     */
    public function __construct(
        public string $id,
        public string $tournamentId,
        public int $round,
        public array $teamIds,
        public ?string $name = null,
        public ?\DateTimeImmutable $scheduledAt = null
    ) {
    }
}
