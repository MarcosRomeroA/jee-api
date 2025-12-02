<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\UpdateBackgroundImage;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTournamentBackgroundImageController extends ApiController
{
    public function __invoke(Request $request, string $tournamentId, string $sessionId): Response
    {
        $input = UpdateTournamentBackgroundImageRequest::fromHttp($request, $tournamentId, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
