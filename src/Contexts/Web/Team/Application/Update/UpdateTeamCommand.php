<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateTeamCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $image = null
    ) {
    }
}

