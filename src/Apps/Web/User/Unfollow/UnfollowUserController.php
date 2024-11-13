<?php declare(strict_types=1);

namespace App\Apps\Web\User\Unfollow;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Unfollow\UnfollowUserCommand;
use Symfony\Component\HttpFoundation\Response;

final class UnfollowUserController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $command = new UnfollowUserCommand(
            $id,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}