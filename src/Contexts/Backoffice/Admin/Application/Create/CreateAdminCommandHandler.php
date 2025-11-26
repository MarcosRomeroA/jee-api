<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Create;

use App\Contexts\Backoffice\Admin\Domain\AdminRepository;
use App\Contexts\Backoffice\Admin\Domain\Exception\AdminUserAlreadyExistsException;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminNameValue;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminPasswordValue;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminUserValue;
use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class CreateAdminCommandHandler implements CommandHandler
{
    public function __construct(
        private AdminCreator $creator,
        private AdminRepository $repository,
    ) {
    }

    public function __invoke(CreateAdminCommand $command): void
    {
        $id = new Uuid($command->id);
        $name = new AdminNameValue($command->name);
        $user = new AdminUserValue($command->user);
        $password = new AdminPasswordValue($command->password);

        // Check if admin user already exists with different ID
        $existingAdminByUser = $this->repository->findByUser($user);
        if ($existingAdminByUser && !$existingAdminByUser->getId()->equals($id)) {
            throw new AdminUserAlreadyExistsException($user->value());
        }

        $this->creator->__invoke($id, $name, $user, $password);
    }
}
