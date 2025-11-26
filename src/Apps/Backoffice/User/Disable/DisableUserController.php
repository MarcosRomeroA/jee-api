<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\User\Disable;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Disable\DisableUserCommand;
use Symfony\Component\HttpFoundation\Response;

final class DisableUserController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new DisableUserCommand($id);
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
