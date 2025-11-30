<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\DeleteAccount;

use App\Contexts\Shared\Domain\CQRS\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DeleteAccountCommandHandler implements CommandHandler
{
    public function __construct(
        private AccountDeleter $deleter,
    ) {
    }

    public function __invoke(DeleteAccountCommand $command): void
    {
        $this->deleter->__invoke(
            new Uuid($command->userId),
        );
    }
}
