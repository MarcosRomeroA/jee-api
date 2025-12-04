<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class CreateEventCommandHandler implements CommandHandler
{
    public function __construct(
        private EventCreator $creator,
    ) {
    }

    public function __invoke(CreateEventCommand $command): void
    {
        $this->creator->createOrUpdate(
            new Uuid($command->id),
            $command->name,
            $command->description,
            $command->gameId !== null ? new Uuid($command->gameId) : null,
            $command->image,
            $command->type,
            $command->startAt,
            $command->endAt,
        );
    }
}
