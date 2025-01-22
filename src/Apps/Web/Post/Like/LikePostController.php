<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Like;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Like\LikePostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LikePostController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        $command = new LikePostCommand(
            $id,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}