<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\DeleteMatch;

use App\Contexts\Shared\Domain\Bus\Command\Command;

final readonly class DeleteMatchCommand implements Command
{
    public function __construct(
        public string $matchId
    ) {
    }
}
