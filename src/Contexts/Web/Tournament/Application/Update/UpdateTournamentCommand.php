<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateTournamentCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public int $maxTeams,
        public bool $isOfficial,
        public ?string $image,
        public ?string $prize,
        public ?string $region,
        public string $startAt,
        public string $endAt
    ) {
    }
}

