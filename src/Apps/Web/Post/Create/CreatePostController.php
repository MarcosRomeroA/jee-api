<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Create\CreatePostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatePostController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        $data = $request->request->all();


        $command = new CreatePostCommand(
            $id,
            $data['body'],
            $data['resources'],
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}