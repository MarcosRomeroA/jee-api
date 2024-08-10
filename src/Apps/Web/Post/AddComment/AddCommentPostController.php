<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddComment;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\AddComment\AddCommentPostCommand;
use Symfony\Component\HttpFoundation\Response;

class AddCommentPostController extends ApiController
{
    public function __invoke(
        AddCommentPostRequest $request,
        string $postId,
        string $sessionId
    ): Response
    {
        $command = new AddCommentPostCommand(
            $postId,
            $sessionId,
            $request->id,
            $request->comment,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}