<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\AddTeam;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\AddTeam\TournamentAddTeamCommand;
use Symfony\Component\HttpFoundation\Response;

final class TournamentAddTeamController extends ApiController
{
    public function __invoke(
        string $tournamentId,
        string $teamId,
        string $sessionId,
    ): Response {
        $command = new TournamentAddTeamCommand(
            $tournamentId,
            $teamId,
            $sessionId,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
