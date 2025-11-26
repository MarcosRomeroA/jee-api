<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Delete;

use App\Contexts\Backoffice\Admin\Domain\AdminRepository;
use App\Contexts\Backoffice\Admin\Domain\Exception\AdminNotFoundException;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class AdminDeleter
{
    public function __construct(
        private AdminRepository $repository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(Uuid $id): void
    {
        $admin = $this->repository->findById($id);

        if (!$admin) {
            throw new AdminNotFoundException($id->value());
        }

        // Soft delete
        $admin->delete();

        $this->repository->save($admin);
        $this->bus->publish($admin->pullDomainEvents());
    }
}
