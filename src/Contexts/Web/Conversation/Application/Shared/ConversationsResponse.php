<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Web\User\Domain\User;

final class ConversationsResponse extends Response
{
    public function __construct(
        private readonly array $conversations,
        private readonly ?User $currentUser = null,
        private readonly ?FileManager $fileManager = null,
    ) {
    }

    public function toArray(): array
    {
        $response = [];

        foreach ($this->conversations as $conversation) {
            $response[] = ConversationResponse::fromEntity($conversation, $this->currentUser, $this->fileManager);
        }

        return $response;
    }
}
