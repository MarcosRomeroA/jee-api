<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\FindByOtherUserId;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindConversationByOtherUserIdQuery implements Query
{
    public function __construct(
        public string $otherUserId,
        public string $sessionId,
    )
    {
    }
}