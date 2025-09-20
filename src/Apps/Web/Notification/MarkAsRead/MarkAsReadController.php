<?php declare(strict_types=1);

namespace App\Apps\Web\Notification\MarkAsRead;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Notification\Application\MarkAsRead\MarkAsReadCommand;
use Symfony\Component\HttpFoundation\Response;

final class MarkAsReadController extends ApiController
{
    public function __invoke(MarkAsReadRequest $request, string $id, string $sessionId): Response
    {
        $command = new MarkAsReadCommand(
            $id,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
