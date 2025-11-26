<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreateAdminCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $user,
        public string $password,
    ) {
    }
}
