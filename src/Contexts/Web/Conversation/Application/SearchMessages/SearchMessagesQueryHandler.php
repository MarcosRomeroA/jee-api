<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\SearchMessages;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\Shared\MessagesResponse;

final readonly class SearchMessagesQueryHandler implements QueryHandler
{
    public function __construct(
        private MessageSearcher $searcher
    )
    {
    }

    public function __invoke(SearchMessagesQuery $query): MessagesResponse
    {
        $sessionId = new Uuid($query->sessionId);

        $conversationId = new Uuid($query->conversationId);

        return $this->searcher->__invoke($sessionId, $conversationId);
    }
}
