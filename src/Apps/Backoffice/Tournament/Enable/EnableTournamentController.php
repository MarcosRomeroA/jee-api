<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Tournament\Enable;

use App\Contexts\Backoffice\Tournament\Application\Enable\EnableTournamentCommand;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class EnableTournamentController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new EnableTournamentCommand(tournamentId: $id);
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
