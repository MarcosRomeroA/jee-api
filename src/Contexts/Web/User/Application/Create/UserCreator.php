<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final readonly class UserCreator
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(
        Uuid $id,
        FirstnameValue $firstname,
        LastnameValue $lastname,
        UsernameValue $username,
        EmailValue $email,
        ?PasswordValue $password,
    ): void {
        // Intentar obtener el usuario por id (upsert)
        try {
            $user = $this->userRepository->findById($id);

            // Si existe, actualizar
            $user->update($firstname, $lastname, $username, $email);

            // Si viene password, actualizarla
            if ($password !== null) {
                $user->updatePassword($password);
            }
        } catch (\Throwable $e) {
            // Si no existe, crear (password obligatoria en creación)
            if ($password === null) {
                throw new \InvalidArgumentException(
                    "Password is required on user creation",
                );
            }

            // Validar que no exista otro usuario con el mismo username
            $this->userRepository->checkIfUsernameExists($username);

            // Validar que no exista otro usuario con el mismo email
            $this->userRepository->checkIfEmailExists($email);

            $user = User::create(
                $id,
                $firstname,
                $lastname,
                $username,
                $email,
                $password,
            );
        }

        $this->userRepository->save($user);
        $this->bus->publish(...$user->pullDomainEvents());
    }
}
