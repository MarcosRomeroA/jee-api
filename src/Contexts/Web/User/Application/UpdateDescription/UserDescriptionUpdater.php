<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateDescription;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class UserDescriptionUpdater
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(
        Uuid $id,
        ?string $description,
    ): void {
        $user = $this->userRepository->findById($id);

        $user->setDescription($description);
        $this->userRepository->save($user);
        $this->bus->publish(...$user->pullDomainEvents());
    }
}
