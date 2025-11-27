<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Post\Enable;

use App\Contexts\Backoffice\Post\Application\Enable\EnablePostCommand;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class EnablePostController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new EnablePostCommand($id);
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
