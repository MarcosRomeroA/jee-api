<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\UpdateMatchResult;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\UpdateMatchResult\UpdateMatchResultCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateMatchResultController extends ApiController
{
    public function __invoke(string $id, Request $request): Response
    {
        $input = UpdateMatchResultRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        $command = new UpdateMatchResultCommand(
            $input->matchId,
            $input->scores,
            $input->winnerId
        );

        $this->commandBus->dispatch($command);

        return new Response('', Response::HTTP_OK);
    }
}
