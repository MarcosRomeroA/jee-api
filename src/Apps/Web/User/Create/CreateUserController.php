<?php declare(strict_types=1);

namespace App\Apps\Web\User\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Create\CreateUserCommand;
use Symfony\Component\HttpFoundation\Response;

final class CreateUserController extends ApiController
{
    public function __invoke(CreateUserRequest $request): Response
    {
        $command = new CreateUserCommand(
            $request->id,
            $request->firstname,
            $request->lastname,
            $request->username,
            $request->email,
            $request->password,
            $request->confirmationPassword,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}