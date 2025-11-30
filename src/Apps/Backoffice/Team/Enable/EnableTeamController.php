<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Team\Enable;

use App\Contexts\Backoffice\Team\Application\Enable\EnableTeamCommand;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class EnableTeamController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new EnableTeamCommand($id);
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
