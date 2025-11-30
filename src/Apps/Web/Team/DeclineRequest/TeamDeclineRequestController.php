<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\DeclineRequest;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\DeclineRequest\TeamDeclineRequestCommand;
use Symfony\Component\HttpFoundation\Response;

final class TeamDeclineRequestController extends ApiController
{
    public function __invoke(
        string $requestId,
        string $sessionId,
    ): Response {
        $command = new TeamDeclineRequestCommand(
            $requestId,
            $sessionId,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
