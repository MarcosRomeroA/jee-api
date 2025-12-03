<?php declare(strict_types=1);

namespace App\Apps\Web\Conversation\MarkMessageAsRead;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Conversation\Application\MarkMessageAsRead\MarkMessageAsReadCommand;
use Symfony\Component\HttpFoundation\Response;

final class MarkMessageAsReadController extends ApiController
{
    public function __invoke(string $conversationId, string $messageId, string $sessionId): Response
    {
        $command = new MarkMessageAsReadCommand($conversationId, $messageId, $sessionId);

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
