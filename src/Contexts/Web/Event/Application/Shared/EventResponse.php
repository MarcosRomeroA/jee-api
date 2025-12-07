<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Event\Domain\Event;

final class EventResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $game,
        public readonly ?string $image,
        public readonly string $type,
        public readonly string $date,
        public readonly string $createdAt,
        public readonly ?string $updatedAt,
    ) {
    }

    public static function fromEvent(Event $event, ?string $cdnBaseUrl = null): self
    {
        $imageUrl = null;
        if ($cdnBaseUrl !== null) {
            $imageUrl = $event->getImageUrl($cdnBaseUrl);
        }

        $gameName = $event->getGame()?->getName();

        $date = self::formatDateRange($event->getStartAt(), $event->getEndAt());

        return new self(
            $event->getId()->value(),
            $event->getName(),
            $event->getDescription(),
            $gameName,
            $imageUrl,
            $event->getType()->value,
            $date,
            $event->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $event->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    private static function formatDateRange(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): string
    {
        $startFormatted = $startAt->format(\DateTimeInterface::ATOM);
        $endFormatted = $endAt->format(\DateTimeInterface::ATOM);

        if ($startFormatted === $endFormatted) {
            return $startFormatted;
        }

        return $startFormatted . ' - ' . $endFormatted;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'game' => $this->game,
            'image' => $this->image,
            'type' => $this->type,
            'date' => $this->date,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
