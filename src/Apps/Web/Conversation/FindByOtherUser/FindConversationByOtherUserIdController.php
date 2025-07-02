<?php declare(strict_types=1);

namespace App\Apps\Web\Conversation\FindByOtherUser;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Conversation\Application\FindByOtherUserId\FindConversationByOtherUserIdQuery;
use Symfony\Component\HttpFoundation\Response;

class FindConversationByOtherUserIdController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $query = new FindConversationByOtherUserIdQuery($id, $sessionId);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}