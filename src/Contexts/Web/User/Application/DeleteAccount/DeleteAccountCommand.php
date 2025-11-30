<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\DeleteAccount;

use App\Contexts\Shared\Domain\CQRS\Command;

final readonly class DeleteAccountCommand implements Command
{
    public function __construct(
        public string $userId,
    ) {
    }
}
