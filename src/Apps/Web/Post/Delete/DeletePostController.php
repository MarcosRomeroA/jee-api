<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Delete;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Delete\DeletePostCommand;
use Symfony\Component\HttpFoundation\Response;

final class DeletePostController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $command = new DeletePostCommand(
            $id,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}