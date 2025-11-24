<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\LeaveTournament;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\LeaveTournament\LeaveTournamentCommand;
use Symfony\Component\HttpFoundation\Response;

final class LeaveTournamentController extends ApiController
{
    public function __invoke(
        string $tournamentId,
        string $teamId,
        string $sessionId,
    ): Response {
        $command = new LeaveTournamentCommand(
            $tournamentId,
            $teamId,
            $sessionId,
        );

        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
