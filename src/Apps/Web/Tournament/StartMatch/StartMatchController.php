<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\StartMatch;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\StartMatch\StartMatchCommand;
use Symfony\Component\HttpFoundation\Response;

final class StartMatchController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $this->commandBus->dispatch(new StartMatchCommand($id));

        return new Response('', Response::HTTP_OK);
    }
}
