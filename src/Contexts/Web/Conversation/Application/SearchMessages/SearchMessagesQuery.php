<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\SearchMessages;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchMessagesQuery implements Query
{
    public function __construct(
        public string $sessionId,
        public string $conversationId
    )
    {
    }
}