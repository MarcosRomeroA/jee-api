<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserDisabler
{
    public function __construct(
        private UserRepository $repository,
        private EventBus $bus,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Uuid $userId): void
    {
        $user = $this->repository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException($userId->value());
        }

        $user->disable();

        $this->entityManager->flush();
        $this->bus->publish($user->pullDomainEvents());
    }
}
