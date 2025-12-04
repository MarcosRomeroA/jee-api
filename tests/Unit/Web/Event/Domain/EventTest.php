<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Event\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Domain\Event;
use App\Contexts\Web\Event\Domain\EventType;
use App\Contexts\Web\Event\Domain\Exception\InvalidEventDateException;
use App\Contexts\Web\Event\Domain\ValueObject\EventDescriptionValue;
use App\Contexts\Web\Event\Domain\ValueObject\EventImageValue;
use App\Contexts\Web\Event\Domain\ValueObject\EventNameValue;
use App\Tests\Unit\Web\Game\Domain\GameMother;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    public function testItShouldCreateAnEvent(): void
    {
        $id = Uuid::random();
        $name = 'Tournament Championship';
        $description = 'Annual championship event';
        $game = GameMother::random();
        $image = 'event-image.jpg';
        $type = EventType::PRESENCIAL;
        $startAt = new \DateTimeImmutable('+1 day');
        $endAt = new \DateTimeImmutable('+2 days');

        $event = Event::create(
            $id,
            new EventNameValue($name),
            new EventDescriptionValue($description),
            $game,
            new EventImageValue($image),
            $type,
            $startAt,
            $endAt,
        );

        $this->assertEquals($id, $event->getId());
        $this->assertEquals($name, $event->getName());
        $this->assertEquals($description, $event->getDescription());
        $this->assertEquals($game, $event->getGame());
        $this->assertEquals($image, $event->getImage());
        $this->assertEquals($type, $event->getType());
        $this->assertEquals($startAt, $event->getStartAt());
        $this->assertEquals($endAt, $event->getEndAt());
        $this->assertNotNull($event->getCreatedAt());
        $this->assertNull($event->getUpdatedAt());
    }

    public function testItShouldCreateAnEventWithoutGame(): void
    {
        $event = EventMother::create(game: null);

        $this->assertNull($event->getGame());
    }

    public function testItShouldCreateAnEventWithoutImage(): void
    {
        $event = EventMother::create(image: null);

        $this->assertNull($event->getImage());
    }

    public function testItShouldCreateAVirtualEvent(): void
    {
        $event = EventMother::virtual();

        $this->assertEquals(EventType::VIRTUAL, $event->getType());
    }

    public function testItShouldCreateAPresencialEvent(): void
    {
        $event = EventMother::presencial();

        $this->assertEquals(EventType::PRESENCIAL, $event->getType());
    }

    public function testItShouldUpdateAnEvent(): void
    {
        $event = EventMother::random();
        $newName = 'Updated Event Name';
        $newDescription = 'Updated description';
        $newGame = GameMother::random();
        $newImage = 'new-image.jpg';
        $newType = EventType::PRESENCIAL;
        $newStartAt = new \DateTimeImmutable('+5 days');
        $newEndAt = new \DateTimeImmutable('+6 days');

        $event->update(
            new EventNameValue($newName),
            new EventDescriptionValue($newDescription),
            $newGame,
            new EventImageValue($newImage),
            $newType,
            $newStartAt,
            $newEndAt,
        );

        $this->assertEquals($newName, $event->getName());
        $this->assertEquals($newDescription, $event->getDescription());
        $this->assertEquals($newGame, $event->getGame());
        $this->assertEquals($newImage, $event->getImage());
        $this->assertEquals($newType, $event->getType());
        $this->assertEquals($newStartAt, $event->getStartAt());
        $this->assertEquals($newEndAt, $event->getEndAt());
        $this->assertNotNull($event->getUpdatedAt());
    }

    public function testItShouldThrowExceptionWhenEndDateIsBeforeStartDate(): void
    {
        $this->expectException(InvalidEventDateException::class);

        $startAt = new \DateTimeImmutable('+2 days');
        $endAt = new \DateTimeImmutable('+1 day');

        EventMother::withDates($startAt, $endAt);
    }

    public function testItShouldAllowSameStartAndEndDate(): void
    {
        $sameDate = new \DateTimeImmutable('+1 day');

        $event = EventMother::withDates($sameDate, $sameDate);

        $this->assertEquals($sameDate, $event->getStartAt());
        $this->assertEquals($sameDate, $event->getEndAt());
    }
}
