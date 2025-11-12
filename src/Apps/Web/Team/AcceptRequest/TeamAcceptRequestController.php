<?php declare(strict_types=1);

namespace App\Apps\Web\Team\AcceptRequest;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\AcceptRequest\TeamAcceptRequestCommand;
use Symfony\Component\HttpFoundation\Response;

final class TeamAcceptRequestController extends ApiController
{
    public function __invoke(string $requestId): Response
    {
        $acceptedByUserId = $this->getAuthenticatedUserId();

        $command = new TeamAcceptRequestCommand($requestId, $acceptedByUserId);

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

