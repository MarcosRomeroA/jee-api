<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\ValueObject\GameAccountDataValue;

final readonly class CreatePlayerCommandHandler implements CommandHandler
{
    public function __construct(
        private PlayerCreator $creator
    ) {
    }

    public function __invoke(CreatePlayerCommand $command): void
    {
        $this->creator->create(
            new Uuid($command->id),
            new Uuid($command->userId),
            new Uuid($command->gameId),
            $command->gameRoleIds,
            new GameAccountDataValue($command->accountData),
        );
    }
}
