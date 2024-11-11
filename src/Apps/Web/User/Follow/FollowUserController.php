<?php declare(strict_types=1);

namespace App\Apps\Web\User\Follow;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Follow\FollowUserCommand;
use Symfony\Component\HttpFoundation\Response;

final class FollowUserController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $command = new FollowUserCommand(
            $id,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}