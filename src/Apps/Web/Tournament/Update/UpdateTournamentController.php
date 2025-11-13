<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Update;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTournamentController extends ApiController
{
    public function __invoke(string $id, Request $request): Response
    {
        $input = UpdateTournamentRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

