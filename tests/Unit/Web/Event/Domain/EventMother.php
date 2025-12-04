<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Event\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Domain\Event;
use App\Contexts\Web\Event\Domain\EventType;
use App\Contexts\Web\Event\Domain\ValueObject\EventDescriptionValue;
use App\Contexts\Web\Event\Domain\ValueObject\EventImageValue;
use App\Contexts\Web\Event\Domain\ValueObject\EventNameValue;
use App\Contexts\Web\Game\Domain\Game;
use App\Tests\Unit\Shared\Domain\ValueObject\UuidMother;
use App\Tests\Unit\Web\Game\Domain\GameMother;

final class EventMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null,
        ?string $description = null,
        ?Game $game = null,
        ?string $image = null,
        ?EventType $type = null,
        ?\DateTimeImmutable $startAt = null,
        ?\DateTimeImmutable $endAt = null,
    ): Event {
        $start = $startAt ?? new \DateTimeImmutable('+1 day');
        $end = $endAt ?? new \DateTimeImmutable('+2 days');

        return Event::create(
            $id ?? UuidMother::create(),
            new EventNameValue($name ?? 'Test Event'),
            new EventDescriptionValue($description ?? 'Test event description'),
            $game,
            new EventImageValue($image),
            $type ?? EventType::VIRTUAL,
            $start,
            $end,
        );
    }

    public static function random(): Event
    {
        return self::create();
    }

    public static function withId(string $id): Event
    {
        return self::create(id: new Uuid($id));
    }

    public static function withName(string $name): Event
    {
        return self::create(name: $name);
    }

    public static function withGame(?Game $game = null): Event
    {
        return self::create(game: $game ?? GameMother::random());
    }

    public static function withType(EventType $type): Event
    {
        return self::create(type: $type);
    }

    public static function presencial(): Event
    {
        return self::create(type: EventType::PRESENCIAL);
    }

    public static function virtual(): Event
    {
        return self::create(type: EventType::VIRTUAL);
    }

    public static function withDates(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): Event
    {
        return self::create(startAt: $startAt, endAt: $endAt);
    }

    public static function upcoming(): Event
    {
        return self::create(
            startAt: new \DateTimeImmutable('+1 hour'),
            endAt: new \DateTimeImmutable('+3 hours'),
        );
    }

    public static function past(): Event
    {
        return self::create(
            startAt: new \DateTimeImmutable('-2 days'),
            endAt: new \DateTimeImmutable('-1 day'),
        );
    }
}
