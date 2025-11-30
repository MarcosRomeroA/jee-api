<?php

declare(strict_types=1);

namespace App\Apps\Web\User\DeleteAccount;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class DeleteAccountController extends ApiController
{
    public function __invoke(string $sessionId): Response
    {
        $input = DeleteAccountRequest::fromHttp($sessionId);
        $command = $input->toCommand();

        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
