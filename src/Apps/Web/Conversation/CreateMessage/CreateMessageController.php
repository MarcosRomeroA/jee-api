<?php declare(strict_types=1);

namespace App\Apps\Web\Conversation\CreateMessage;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Conversation\Application\CreateMessage\CreateMessageCommand;
use Symfony\Component\HttpFoundation\Response;

final class CreateMessageController extends ApiController
{
    public function __invoke(CreateMessageRequest $request, string $conversationId, string $messageId, string $sessionId): Response
    {
        $command = new CreateMessageCommand($conversationId, $sessionId, $messageId, $request->content);

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}