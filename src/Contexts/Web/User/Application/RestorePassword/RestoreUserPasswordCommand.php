<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\RestorePassword;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

class RestoreUserPasswordCommand implements Command
{
    public function __construct(
        public string $id,
        public string $newPassword,
        public string $confirmationNewPassword,
    )
    {
    }
}