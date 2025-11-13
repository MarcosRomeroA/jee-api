<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Player\Application\Create\CreatePlayerCommand;
use Symfony\Component\HttpFoundation\Response;

final class CreatePlayerController extends ApiController
{
    public function __invoke(CreatePlayerRequest $request): Response
    {
        $command = new CreatePlayerCommand(
            $request->id,
            $request->userId,
            $request->gameId,
            $request->gameRoleIds,
            $request->gameRankId ?? null,
            $request->username
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

