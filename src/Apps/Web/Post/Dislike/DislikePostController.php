<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Dislike;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Dislike\DislikePostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DislikePostController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        $command = new DislikePostCommand(
            $id,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}