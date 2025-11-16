<?php declare(strict_types=1);

namespace App\Apps\Web\Team\UpdateLeader;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTeamLeaderController extends ApiController
{
    public function __invoke(
        string $id,
        string $userId,
        string $sessionId,
    ): Response {
        $input = new UpdateTeamLeaderRequest($id, $userId, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
