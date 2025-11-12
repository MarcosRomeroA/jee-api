<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreateTeamCommand implements Command
{
    public function __construct(
        public string $id,
        public string $gameId,
        public string $ownerId,
        public string $name,
        public ?string $image = null
    ) {
    }
}

