<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostComment;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\AddComment\AddCommentPostCommand;
use Symfony\Component\HttpFoundation\Response;

class AddPostCommentController extends ApiController
{
    public function __invoke(
        AddPostCommentRequest $request,
        string $id,
        string $sessionId
    ): Response
    {
        $command = new AddCommentPostCommand(
            $id,
            $sessionId,
            $request->commentId,
            $request->commentBody,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}