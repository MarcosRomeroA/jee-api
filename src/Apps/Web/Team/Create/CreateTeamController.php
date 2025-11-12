<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\Create\CreateTeamCommand;
use Symfony\Component\HttpFoundation\Response;

final class CreateTeamController extends ApiController
{
    public function __invoke(CreateTeamRequest $request): Response
    {
        $command = new CreateTeamCommand(
            $request->id,
            $request->gameId,
            $request->name,
            $request->image
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

