<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\RequestAccess;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\RequestAccess\TournamentRequestAccessCommand;
use Symfony\Component\HttpFoundation\Response;

final class TournamentRequestAccessController extends ApiController
{
    public function __invoke(
        string $tournamentId,
        string $teamId,
    ): Response {
        $command = new TournamentRequestAccessCommand(
            $tournamentId,
            $teamId,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
