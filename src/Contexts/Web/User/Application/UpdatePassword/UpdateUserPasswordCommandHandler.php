<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdatePassword;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\Exception\PasswordMismatchException;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;

final readonly class UpdateUserPasswordCommandHandler implements CommandHandler
{
    public function __construct(
        private UserPasswordUpdater $updater,
    )
    {
    }

    /**
     * @throws PasswordMismatchException
     */
    public function __invoke(UpdateUserPasswordCommand $command): void
    {
        $id = new Uuid($command->id);

        if ($command->newPassword !== $command->confirmationNewPassword) {
            throw new PasswordMismatchException();
        }

        $newPassword = new PasswordValue($command->newPassword);

        $this->updater->__invoke($id, $command->oldPassword, $newPassword);
    }
}