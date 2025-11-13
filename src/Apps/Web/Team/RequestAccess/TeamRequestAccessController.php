<?php declare(strict_types=1);

namespace App\Apps\Web\Team\RequestAccess;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TeamRequestAccessController extends ApiController
{
    public function __invoke(Request $request, string $teamId): Response
    {
        $input = TeamRequestAccessRequest::fromHttp($request, $teamId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

