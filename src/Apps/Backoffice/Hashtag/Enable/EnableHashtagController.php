<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Hashtag\Enable;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnableHashtagController extends ApiController
{
    public function __invoke(Request $request, string $id): Response
    {
        $input = EnableHashtagRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
