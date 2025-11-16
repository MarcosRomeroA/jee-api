<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTeamController extends ApiController
{
    public function __invoke(
        string $id,
        string $sessionId,
        Request $request,
    ): Response {
        $input = CreateTeamRequest::fromHttp($request, $id, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
