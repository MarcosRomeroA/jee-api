<?php

declare(strict_types=1);

namespace App\Apps\Web\User\ConfirmEmail;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\ConfirmEmail\ConfirmEmailCommand;
use Symfony\Component\HttpFoundation\Response;

final class ConfirmEmailController extends ApiController
{
    public function __invoke(string $token): Response
    {
        $command = new ConfirmEmailCommand($token);

        $this->commandBus->dispatch($command);

        return new Response('', Response::HTTP_OK);
    }
}
