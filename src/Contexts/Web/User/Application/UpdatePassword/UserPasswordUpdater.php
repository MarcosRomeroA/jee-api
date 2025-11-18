<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdatePassword;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\Exception\CurrentPasswordMismatchException;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;

final readonly class UserPasswordUpdater
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(
        Uuid $id,
        string $oldPassword,
        PasswordValue $newPassword,
    ): void {
        $user = $this->userRepository->findById($id);

        if ($user->getPassword()->verifyPassword($oldPassword) === false) {
            throw new CurrentPasswordMismatchException();
        }

        $user->updatePassword($newPassword);
        $this->userRepository->save($user);
        $this->bus->publish($user->pullDomainEvents());
    }
}
