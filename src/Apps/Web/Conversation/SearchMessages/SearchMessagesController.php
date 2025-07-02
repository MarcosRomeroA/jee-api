<?php declare(strict_types=1);

namespace App\Apps\Web\Conversation\SearchMessages;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Conversation\Application\SearchMessages\SearchMessagesQuery;
use Symfony\Component\HttpFoundation\Response;

class SearchMessagesController extends ApiController
{
    public function __invoke(string $conversationId,string $sessionId): Response
    {
        $query = new SearchMessagesQuery($sessionId, $conversationId);

        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}