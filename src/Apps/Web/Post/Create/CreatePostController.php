<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Create\CreatePostCommand;
use Symfony\Component\HttpFoundation\Response;

class CreatePostController extends ApiController
{
    public function __invoke(CreatePostRequest $request, string $sessionId): Response
    {
        $command = new CreatePostCommand(
            $request->id,
            $request->body,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}