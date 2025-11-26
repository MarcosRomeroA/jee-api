<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Admin\Delete;

use App\Contexts\Backoffice\Admin\Application\Delete\DeleteAdminCommand;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class DeleteAdminController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new DeleteAdminCommand($id);
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
