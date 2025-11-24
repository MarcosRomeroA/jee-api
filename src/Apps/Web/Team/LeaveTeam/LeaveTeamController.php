<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\LeaveTeam;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\LeaveTeam\LeaveTeamCommand;
use Symfony\Component\HttpFoundation\Response;

final class LeaveTeamController extends ApiController
{
    public function __invoke(
        string $id,
        string $sessionId,
    ): Response {
        $command = new LeaveTeamCommand(
            $id,
            $sessionId,
        );

        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
