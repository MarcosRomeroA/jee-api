<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Delete;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\Exception\EventNotFoundException;

final readonly class EventDeleter
{
    public function __construct(
        private EventRepository $eventRepository,
    ) {
    }

    public function __invoke(Uuid $id): void
    {
        $event = $this->eventRepository->findById($id);

        if ($event === null) {
            throw new EventNotFoundException($id->value());
        }

        $this->eventRepository->delete($event);
    }
}
