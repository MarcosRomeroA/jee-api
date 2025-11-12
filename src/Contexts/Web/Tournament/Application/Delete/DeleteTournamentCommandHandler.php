<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class DeleteTournamentCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TournamentDeleter $deleter
    ) {
    }

    public function __invoke(DeleteTournamentCommand $command): void
    {
        $this->deleter->delete(new Uuid($command->id));
    }
}

