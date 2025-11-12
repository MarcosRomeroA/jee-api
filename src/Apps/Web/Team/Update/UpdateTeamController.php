<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Update;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\Update\UpdateTeamCommand;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTeamController extends ApiController
{
    public function __invoke(string $id, UpdateTeamRequest $request): Response
    {
        $command = new UpdateTeamCommand(
            $id,
            $request->name,
            $request->image
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

