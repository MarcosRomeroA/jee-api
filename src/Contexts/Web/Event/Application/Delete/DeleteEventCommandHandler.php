<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DeleteEventCommandHandler implements CommandHandler
{
    public function __construct(
        private EventDeleter $deleter,
    ) {
    }

    public function __invoke(DeleteEventCommand $command): void
    {
        $this->deleter->__invoke(new Uuid($command->id));
    }
}
