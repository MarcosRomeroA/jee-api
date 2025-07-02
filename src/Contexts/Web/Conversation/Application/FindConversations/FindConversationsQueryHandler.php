<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\FindConversations;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\Shared\ConversationsResponse;

final readonly class FindConversationsQueryHandler implements QueryHandler
{
    public function __construct(
        private ConversationsFinder $finder
    )
    {
    }

    public function __invoke(FindConversationsQuery $query): ConversationsResponse
    {
        $sessionId = new Uuid($query->sessionId);

        return $this->finder->__invoke($sessionId);
    }
}
