<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Team\Disable;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DisableTeamController extends ApiController
{
    public function __invoke(Request $request, string $id): Response
    {
        $input = DisableTeamRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
