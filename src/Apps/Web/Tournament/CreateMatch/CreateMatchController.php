<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\CreateMatch;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\CreateMatch\CreateMatchCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateMatchController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = CreateMatchRequest::fromHttp($request);
        $this->validateRequest($input);

        $command = new CreateMatchCommand(
            $input->id,
            $input->tournamentId,
            $input->round,
            $input->teamIds,
            $input->name,
            $input->getScheduledAtAsDateTime()
        );

        $this->commandBus->dispatch($command);

        return new Response('', Response::HTTP_OK);
    }
}
