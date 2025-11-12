<?php declare(strict_types=1);

namespace App\Apps\Web\Team\RequestAccess;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\RequestAccess\TeamRequestAccessCommand;
use Symfony\Component\HttpFoundation\Response;

final class TeamRequestAccessController extends ApiController
{
    public function __invoke(string $teamId, TeamRequestAccessRequest $request): Response
    {
        $command = new TeamRequestAccessCommand(
            $teamId,
            $request->playerId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

