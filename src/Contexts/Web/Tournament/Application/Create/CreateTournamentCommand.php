<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreateTournamentCommand implements Command
{
    public function __construct(
        public string $id,
        public string $gameId,
        public string $name,
        public bool $isOfficial,
        public string $responsibleId,
        public string $creatorId,
        public ?string $description = null,
        public ?string $rules = null,
        public ?int $maxTeams = null,
        public ?string $image = null,
        public ?string $prize = null,
        public ?string $region = null,
        public ?string $startAt = null,
        public ?string $endAt = null,
        public ?string $minGameRankId = null,
        public ?string $maxGameRankId = null,
    ) {
    }
}
