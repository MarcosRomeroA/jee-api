<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\DeclineRequest;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\DeclineRequest\TournamentDeclineRequestCommand;
use Symfony\Component\HttpFoundation\Response;

final class TournamentDeclineRequestController extends ApiController
{
    public function __invoke(
        string $requestId,
        string $sessionId,
    ): Response {
        $command = new TournamentDeclineRequestCommand(
            $requestId,
            $sessionId,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
