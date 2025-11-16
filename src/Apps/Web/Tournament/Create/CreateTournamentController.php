<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTournamentController extends ApiController
{
    public function __invoke(
        string $id,
        string $sessionId,
        Request $request,
    ): Response {
        $input = CreateTournamentRequest::fromHttp($request, $id, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
