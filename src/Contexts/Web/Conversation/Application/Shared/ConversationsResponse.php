<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class ConversationsResponse extends Response
{
    public function __construct(
        private readonly array $conversations
    )
    {
    }

    public function toArray(): array
    {
        $response = [];

        foreach($this->conversations as $conversation){
            $response[] = ConversationResponse::fromEntity($conversation);
        }

        return $response;
    }
}