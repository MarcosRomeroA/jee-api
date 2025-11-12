<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Delete;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\Delete\DeleteTournamentCommand;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTournamentController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new DeleteTournamentCommand($id);

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

