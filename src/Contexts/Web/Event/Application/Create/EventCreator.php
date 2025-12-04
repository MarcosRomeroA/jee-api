<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Create;

use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Domain\Event;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\EventType;
use App\Contexts\Web\Event\Domain\Exception\InvalidEventTypeException;
use App\Contexts\Web\Event\Domain\ValueObject\EventDescriptionValue;
use App\Contexts\Web\Event\Domain\ValueObject\EventImageValue;
use App\Contexts\Web\Event\Domain\ValueObject\EventNameValue;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRepository;

final readonly class EventCreator
{
    public function __construct(
        private EventRepository $eventRepository,
        private GameRepository $gameRepository,
        private ImageUploader $imageUploader,
    ) {
    }

    public function createOrUpdate(
        Uuid $id,
        string $name,
        ?string $description,
        ?Uuid $gameId,
        ?string $image,
        string $type,
        string $startAt,
        string $endAt,
    ): void {
        $existingEvent = $this->eventRepository->findById($id);

        if ($existingEvent !== null) {
            $this->update($existingEvent, $name, $description, $gameId, $image, $type, $startAt, $endAt);
        } else {
            $this->create($id, $name, $description, $gameId, $image, $type, $startAt, $endAt);
        }
    }

    private function create(
        Uuid $id,
        string $name,
        ?string $description,
        ?Uuid $gameId,
        ?string $image,
        string $type,
        string $startAt,
        string $endAt,
    ): void {
        $game = $this->findGame($gameId);
        $eventType = $this->parseEventType($type);
        $imageFilename = $this->processImage($id->value(), $image);

        $event = Event::create(
            $id,
            new EventNameValue($name),
            new EventDescriptionValue($description),
            $game,
            new EventImageValue($imageFilename),
            $eventType,
            new \DateTimeImmutable($startAt),
            new \DateTimeImmutable($endAt),
        );

        $this->eventRepository->save($event);
    }

    private function update(
        Event $event,
        string $name,
        ?string $description,
        ?Uuid $gameId,
        ?string $image,
        string $type,
        string $startAt,
        string $endAt,
    ): void {
        $game = $this->findGame($gameId);
        $eventType = $this->parseEventType($type);
        $imageFilename = $this->processImage($event->getId()->value(), $image, $event->getImage());

        $event->update(
            new EventNameValue($name),
            new EventDescriptionValue($description),
            $game,
            new EventImageValue($imageFilename),
            $eventType,
            new \DateTimeImmutable($startAt),
            new \DateTimeImmutable($endAt),
        );

        $this->eventRepository->save($event);
    }

    private function findGame(?Uuid $gameId): ?Game
    {
        if ($gameId === null) {
            return null;
        }

        return $this->gameRepository->findById($gameId);
    }

    private function parseEventType(string $type): EventType
    {
        $eventType = EventType::tryFrom($type);

        if ($eventType === null) {
            throw new InvalidEventTypeException($type);
        }

        return $eventType;
    }

    private function processImage(string $eventId, ?string $image, ?string $currentImage = null): ?string
    {
        if ($image === null) {
            return $currentImage;
        }

        if ($this->imageUploader->isBase64Image($image)) {
            return $this->imageUploader->upload($image, 'event/' . $eventId);
        }

        return $currentImage;
    }
}
