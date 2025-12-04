<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Event\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateEventController extends ApiController
{
    public function __invoke(string $id, Request $request): Response
    {
        $input = CreateEventRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
