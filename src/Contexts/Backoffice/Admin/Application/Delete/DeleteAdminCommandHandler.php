<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DeleteAdminCommandHandler implements CommandHandler
{
    public function __construct(
        private AdminDeleter $deleter
    ) {
    }

    public function __invoke(DeleteAdminCommand $command): void
    {
        $id = new Uuid($command->id);
        $this->deleter->__invoke($id);
    }
}
