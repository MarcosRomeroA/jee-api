<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DeleteAdminCommand implements Command
{
    public function __construct(
        public string $id
    ) {
    }
}
