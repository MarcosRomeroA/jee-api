<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FinalizeActiveTournaments;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class FinalizeActiveTournamentsCommand implements Command
{
    public function __construct()
    {
    }
}
