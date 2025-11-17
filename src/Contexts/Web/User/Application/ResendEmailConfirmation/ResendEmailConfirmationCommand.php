<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\ResendEmailConfirmation;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final class ResendEmailConfirmationCommand implements Command
{
    public function __construct(
        private readonly string $userId
    ) {
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
