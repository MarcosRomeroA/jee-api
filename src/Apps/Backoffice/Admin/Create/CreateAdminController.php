<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Admin\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateAdminController extends ApiController
{
    public function __invoke(string $id, Request $request): Response
    {
        $input = CreateAdminRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successCreatedResponse();
    }
}
