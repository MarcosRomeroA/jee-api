<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreateEventCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public ?string $gameId,
        public ?string $image,
        public string $type,
        public string $startAt,
        public string $endAt,
    ) {
    }
}
