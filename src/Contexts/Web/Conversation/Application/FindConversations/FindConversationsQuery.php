<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\FindConversations;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindConversationsQuery implements Query
{
    public function __construct(
        public string $sessionId,
    )
    {
    }
}