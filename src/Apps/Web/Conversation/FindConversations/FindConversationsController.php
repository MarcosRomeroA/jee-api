<?php declare(strict_types=1);

namespace App\Apps\Web\Conversation\FindConversations;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Conversation\Application\FindConversations\FindConversationsQuery;
use Symfony\Component\HttpFoundation\Response;

class FindConversationsController extends ApiController
{
    public function __invoke(string $sessionId): Response
    {
        $query = new FindConversationsQuery($sessionId);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}