<?php declare(strict_types=1);

namespace App\Apps\Web\User\RestorePassword;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\RestorePassword\RestoreUserPasswordCommand;
use Symfony\Component\HttpFoundation\Response;

final class RestoreUserPasswordController extends ApiController
{
    public function __invoke(RestoreUserPasswordRequest $request, string $id): Response
    {
        $command = new RestoreUserPasswordCommand(
            $id,
            $request->newPassword,
            $request->confirmationNewPassword,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}