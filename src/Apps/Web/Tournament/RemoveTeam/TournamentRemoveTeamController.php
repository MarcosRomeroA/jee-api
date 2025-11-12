<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\RemoveTeam;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\RemoveTeam\TournamentRemoveTeamCommand;
use Symfony\Component\HttpFoundation\Response;

final class TournamentRemoveTeamController extends ApiController
{
    public function __invoke(string $tournamentId, string $teamId): Response
    {
        $command = new TournamentRemoveTeamCommand($tournamentId, $teamId);

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

