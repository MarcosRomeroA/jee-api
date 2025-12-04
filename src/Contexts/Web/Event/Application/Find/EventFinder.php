<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Find;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Application\Shared\EventResponse;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\Exception\EventNotFoundException;

final readonly class EventFinder
{
    public function __construct(
        private EventRepository $eventRepository,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(Uuid $id): EventResponse
    {
        $event = $this->eventRepository->findById($id);

        if ($event === null) {
            throw new EventNotFoundException($id->value());
        }

        return EventResponse::fromEvent($event, $this->fileManager);
    }
}
