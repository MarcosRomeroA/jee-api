<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class MessagesResponse extends Response
{
    public function __construct(
        private readonly array $messages,
        private readonly string $mercureToken,
        private readonly string $userId
    )
    {
    }

    public function toArray(): array
    {
        $response['data'] = [];
        $response['metadata']['mercureToken'] = $this->mercureToken;

        foreach($this->messages as $message){
            $response['data'][] = MessageResponse::fromEntity($message, $this->userId);
        }

        return $response;
    }
}