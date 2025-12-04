<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DeleteEventCommand implements Command
{
    public function __construct(
        public string $id,
    ) {
    }
}
