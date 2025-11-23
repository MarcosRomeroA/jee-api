<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\AcceptRequest;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\AcceptRequest\TournamentAcceptRequestCommand;
use Symfony\Component\HttpFoundation\Response;

final class TournamentAcceptRequestController extends ApiController
{
    public function __invoke(
        string $requestId,
        string $sessionId,
    ): Response {
        $command = new TournamentAcceptRequestCommand(
            $requestId,
            $sessionId,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
