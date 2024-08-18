<?php declare(strict_types=1);

namespace App\Apps\Web\User\Update;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Update\UpdateUserCommand;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserController extends ApiController
{
    public function __invoke(UpdateUserRequest $request, string $id): Response
    {
        $command = new UpdateUserCommand(
            $id,
            $request->firstname,
            $request->lastname,
            $request->username,
            $request->email,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}