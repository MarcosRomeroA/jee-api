<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\StartMatch;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class StartMatchCommand implements Command
{
    public function __construct(
        public string $matchId
    ) {
    }
}
