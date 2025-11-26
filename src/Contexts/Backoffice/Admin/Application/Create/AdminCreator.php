<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Create;

use App\Contexts\Backoffice\Admin\Domain\Admin;
use App\Contexts\Backoffice\Admin\Domain\AdminRepository;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminNameValue;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminPasswordValue;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminUserValue;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class AdminCreator
{
    public function __construct(
        private AdminRepository $repository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(
        Uuid $id,
        AdminNameValue $name,
        AdminUserValue $user,
        AdminPasswordValue $password,
    ): void {
        // UPSERT logic: check if admin exists
        $existingAdmin = $this->repository->findById($id);

        if ($existingAdmin) {
            // Update existing admin
            $existingAdmin->update($name, $user);
            // Only update password if provided (non-empty)
            if (!empty($password->value())) {
                $existingAdmin->updatePassword($password);
            }
            $admin = $existingAdmin;
        } else {
            // Create new admin
            $admin = Admin::create($id, $name, $user, $password);
        }

        $this->repository->save($admin);
        $this->bus->publish($admin->pullDomainEvents());
    }
}
