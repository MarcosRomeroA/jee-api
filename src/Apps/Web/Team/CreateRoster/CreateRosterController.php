<?php declare(strict_types=1);

namespace App\Apps\Web\Team\CreateRoster;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateRosterController extends ApiController
{
    public function __invoke(
        string $teamId,
        string $rosterId,
        string $sessionId,
        Request $request,
    ): Response {
        $input = CreateRosterRequest::fromHttp($request, $rosterId, $teamId, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
