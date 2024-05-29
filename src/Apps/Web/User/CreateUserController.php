<?php declare(strict_types=1);

namespace App\Apps\Web\User;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Create\CreateUserCommand;
use Symfony\Component\HttpFoundation\Request;

class CreateUserController extends ApiController
{
    public function __invoke(Request $request): void
    {
        $data = json_decode($request->getContent());

        $command = new CreateUserCommand(
            $data->id,
            $data->firstname,
            $data->lastname,
            $data->username,
            $data->email,
            $data->password,
            $data->confirmationPassword,
        );

        $this->commandBus->dispatch($command);

        $this->successEmptyResponse();
    }

    protected function exceptions(): array
    {
        return [];
    }
}