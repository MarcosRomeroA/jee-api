<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Tournament\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTournamentController extends ApiController
{
    public function __invoke(Request $request, string $id): Response
    {
        $input = CreateTournamentRequest::fromHttp($request, $id);
        $this->validateRequest($input);
        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}

