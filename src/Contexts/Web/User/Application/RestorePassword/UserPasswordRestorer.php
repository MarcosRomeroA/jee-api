<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\RestorePassword;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\Exception\CurrentPasswordMismatchException;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;

final readonly class UserPasswordRestorer
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(
        Uuid $id,
        PasswordValue $newPassword,
    ): void {
        $user = $this->userRepository->findById($id);
        $user->updatePassword($newPassword);
        $this->userRepository->save($user);
        $this->bus->publish($user->pullDomainEvents());
    }
}
