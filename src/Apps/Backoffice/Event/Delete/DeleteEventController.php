<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Event\Delete;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Event\Application\Delete\DeleteEventCommand;
use Symfony\Component\HttpFoundation\Response;

final class DeleteEventController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new DeleteEventCommand($id);
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
