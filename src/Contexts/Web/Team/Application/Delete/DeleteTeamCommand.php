<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DeleteTeamCommand implements Command
{
    public function __construct(
        public string $id
    ) {
    }
}

