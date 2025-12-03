<?php declare(strict_types=1);

namespace App\Apps\Web\Team\RemovePlayerFromRoster;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\RemovePlayerFromRoster\RemovePlayerFromRosterCommand;
use Symfony\Component\HttpFoundation\Response;

final class RemovePlayerFromRosterController extends ApiController
{
    public function __invoke(
        string $teamId,
        string $rosterId,
        string $playerId,
        string $sessionId,
    ): Response {
        $command = new RemovePlayerFromRosterCommand(
            $rosterId,
            $teamId,
            $playerId,
            $sessionId,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
