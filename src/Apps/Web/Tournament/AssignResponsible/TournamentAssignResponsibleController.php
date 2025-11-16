<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\AssignResponsible;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\AssignResponsible\TournamentAssignResponsibleCommand;
use Symfony\Component\HttpFoundation\Response;

final class TournamentAssignResponsibleController extends ApiController
{
    public function __invoke(
        string $tournamentId,
        string $userId,
        string $sessionId,
    ): Response {
        $command = new TournamentAssignResponsibleCommand(
            $tournamentId,
            $userId,
            $sessionId,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
