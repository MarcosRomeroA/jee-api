<?php

declare(strict_types=1);

namespace App\Apps\Web\User\UpdateDescription;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\UpdateDescription\UpdateUserDescriptionCommand;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateUserDescriptionController extends ApiController
{
    public function __invoke(UpdateUserDescriptionRequest $request, string $sessionId): Response
    {
        $command = new UpdateUserDescriptionCommand(
            $sessionId,
            $request->description ?? null,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
