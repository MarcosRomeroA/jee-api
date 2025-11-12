<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class DeletePlayerCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly PlayerDeleter $deleter
    ) {
    }

    public function __invoke(DeletePlayerCommand $command): void
    {
        $this->deleter->delete(new Uuid($command->id));
    }
}

