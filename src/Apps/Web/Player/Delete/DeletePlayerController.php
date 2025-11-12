<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Delete;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Player\Application\Delete\DeletePlayerCommand;
use Symfony\Component\HttpFoundation\Response;

final class DeletePlayerController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new DeletePlayerCommand($id);

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}

