<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateTournamentBackgroundImageCommand implements Command
{
    public function __construct(
        public string $tournamentId,
        public string $requesterId,
        public string $image,
    ) {
    }
}
