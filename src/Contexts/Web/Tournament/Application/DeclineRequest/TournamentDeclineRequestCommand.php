<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\DeclineRequest;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class TournamentDeclineRequestCommand implements Command
{
    public function __construct(
        public string $requestId,
        public string $declinedByUserId,
    ) {
    }
}
