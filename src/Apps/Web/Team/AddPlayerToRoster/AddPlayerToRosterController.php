<?php declare(strict_types=1);

namespace App\Apps\Web\Team\AddPlayerToRoster;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddPlayerToRosterController extends ApiController
{
    public function __invoke(
        string $teamId,
        string $rosterId,
        string $rosterPlayerId,
        string $sessionId,
        Request $request,
    ): Response {
        $input = AddPlayerToRosterRequest::fromHttp($request, $teamId, $rosterId, $rosterPlayerId, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
