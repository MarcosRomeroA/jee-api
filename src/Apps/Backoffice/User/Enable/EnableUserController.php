<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\User\Enable;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Enable\EnableUserCommand;
use Symfony\Component\HttpFoundation\Response;

final class EnableUserController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new EnableUserCommand($id);
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
