<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdatePassword;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateUserPasswordCommand implements Command
{
    public function __construct(
        public string $id,
        public string $oldPassword,
        public string $newPassword,
        public string $confirmationNewPassword,
    )
    {
    }
}