<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\Create\CreateTournamentCommand;
use Symfony\Component\HttpFoundation\Response;

final class CreateTournamentController extends ApiController
{
    public function __invoke(CreateTournamentRequest $request): Response
    {
        $command = new CreateTournamentCommand(
            $request->id,
            $request->gameId,
            $request->responsibleId,
            $request->name,
            $request->description,
            $request->maxTeams,
            $request->isOfficial,
            $request->image,
            $request->prize,
            $request->region,
            $request->startAt,
            $request->endAt,
            $request->minGameRankId,
            $request->maxGameRankId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

