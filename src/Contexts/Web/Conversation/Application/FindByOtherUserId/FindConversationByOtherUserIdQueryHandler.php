<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\FindByOtherUserId;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\Shared\ConversationResponse;

final readonly class FindConversationByOtherUserIdQueryHandler implements QueryHandler
{
    public function __construct(
        private ConversationFinder $finder
    )
    {
    }

    public function __invoke(FindConversationByOtherUserIdQuery $query): ConversationResponse
    {
        $otherUserId = new Uuid($query->otherUserId);
        $sessionId = new Uuid($query->sessionId);

        return $this->finder->__invoke($otherUserId, $sessionId);
    }
}
