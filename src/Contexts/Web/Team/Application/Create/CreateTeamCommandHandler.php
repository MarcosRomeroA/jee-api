<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class CreateTeamCommandHandler implements CommandHandler
{
    public function __construct(private TeamCreator $creator)
    {
    }

    public function __invoke(CreateTeamCommand $command): void
    {
        $this->creator->createOrUpdate(
            new Uuid($command->id),
            $command->name,
            $command->description,
            $command->image,
            new Uuid($command->requesterId),
        );
    }
}
