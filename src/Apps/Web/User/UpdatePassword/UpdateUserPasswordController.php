<?php declare(strict_types=1);

namespace App\Apps\Web\User\UpdatePassword;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\UpdatePassword\UpdateUserPasswordCommand;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserPasswordController extends ApiController
{
    public function __invoke(UpdateUserPasswordRequest $request, string $id): Response
    {
        $command = new UpdateUserPasswordCommand(
            $id,
            $request->oldPassword,
            $request->newPassword,
            $request->confirmationNewPassword,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}