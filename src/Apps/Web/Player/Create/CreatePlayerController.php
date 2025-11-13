<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreatePlayerController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        $input = CreatePlayerRequest::fromHttp($request, $id, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

