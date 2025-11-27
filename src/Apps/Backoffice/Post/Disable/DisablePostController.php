<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Post\Disable;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DisablePostController extends ApiController
{
    public function __invoke(Request $request, string $id): Response
    {
        $input = DisablePostRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
