<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\RemoveUser;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\RemoveUser\RemoveUserCommand;
use Symfony\Component\HttpFoundation\Response;

final class RemoveTeamUserController extends ApiController
{
    public function __invoke(
        string $teamId,
        string $userId,
        string $sessionId,
    ): Response {
        $command = new RemoveUserCommand(
            $teamId,
            $userId,
            $sessionId,
        );

        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
