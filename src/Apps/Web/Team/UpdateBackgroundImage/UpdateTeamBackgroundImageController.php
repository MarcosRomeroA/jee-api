<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\UpdateBackgroundImage;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTeamBackgroundImageController extends ApiController
{
    public function __invoke(Request $request, string $teamId, string $sessionId): Response
    {
        $input = UpdateTeamBackgroundImageRequest::fromHttp($request, $teamId, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
