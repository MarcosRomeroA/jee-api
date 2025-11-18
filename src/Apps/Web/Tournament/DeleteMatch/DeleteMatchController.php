<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\DeleteMatch;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\DeleteMatch\DeleteMatchCommand;
use Symfony\Component\HttpFoundation\Response;

final class DeleteMatchController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $this->commandBus->dispatch(new DeleteMatchCommand($id));

        return new Response('', Response::HTTP_OK);
    }
}
