<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateUserCommand implements Command
{
    public function __construct(
        public string $id,
        public ?string $firstname,
        public ?string $lastname,
        public ?string $username,
        public ?string $email,
    ) {
    }
}
