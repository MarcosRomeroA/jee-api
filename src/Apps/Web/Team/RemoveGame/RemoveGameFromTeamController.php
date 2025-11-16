<?php declare(strict_types=1);

namespace App\Apps\Web\Team\RemoveGame;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\RemoveGame\RemoveGameFromTeamCommand;
use Symfony\Component\HttpFoundation\Response;

final class RemoveGameFromTeamController extends ApiController
{
    public function __invoke(string $id, string $gameId): Response
    {
        $command = new RemoveGameFromTeamCommand($id, $gameId);
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
