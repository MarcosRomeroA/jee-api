<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class DeleteTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TeamDeleter $deleter
    ) {
    }

    public function __invoke(DeleteTeamCommand $command): void
    {
        $this->deleter->delete(new Uuid($command->id));
    }
}

