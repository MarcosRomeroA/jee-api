<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Delete;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\Delete\DeleteTeamCommand;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTeamController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new DeleteTeamCommand($id);

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

