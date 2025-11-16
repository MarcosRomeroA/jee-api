<?php

declare(strict_types=1);

namespace App\Apps\Web\Player\Update;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Player\Application\Update\UpdatePlayerCommand;
use Symfony\Component\HttpFoundation\Response;

final class UpdatePlayerController extends ApiController
{
    public function __invoke(string $id, UpdatePlayerRequest $request): Response
    {
        $command = new UpdatePlayerCommand(
            $id,
            $request->username,
            $request->getGameRoleIds(),
            $request->getGameRankId()
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
