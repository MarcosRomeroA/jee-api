<?php declare(strict_types=1);

namespace App\Apps\Web\Team\DeleteRoster;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteRosterController extends ApiController
{
    public function __invoke(
        string $teamId,
        string $rosterId,
        string $sessionId,
        Request $request,
    ): Response {
        $input = DeleteRosterRequest::fromHttp($request, $teamId, $rosterId, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

