<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\SetFinalPositions;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetFinalPositionsController extends ApiController
{
    public function __invoke(
        string $tournamentId,
        string $sessionId,
        Request $request,
    ): Response {
        $input = SetFinalPositionsRequest::fromHttp($request, $tournamentId, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
