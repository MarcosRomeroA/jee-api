<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Update;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\Update\UpdateTournamentCommand;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTournamentController extends ApiController
{
    public function __invoke(string $id, UpdateTournamentRequest $request): Response
    {
        $command = new UpdateTournamentCommand(
            $id,
            $request->name,
            $request->description,
            $request->maxTeams,
            $request->isOfficial,
            $request->image,
            $request->prize,
            $request->region,
            $request->startAt,
            $request->endAt
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

