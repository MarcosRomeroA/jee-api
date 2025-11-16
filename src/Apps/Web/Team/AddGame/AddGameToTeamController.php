<?php declare(strict_types=1);

namespace App\Apps\Web\Team\AddGame;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\AddGame\AddGameToTeamCommand;
use Symfony\Component\HttpFoundation\Response;

final class AddGameToTeamController extends ApiController
{
    public function __invoke(string $id, string $gameId): Response
    {
        $command = new AddGameToTeamCommand($id, $gameId);
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
