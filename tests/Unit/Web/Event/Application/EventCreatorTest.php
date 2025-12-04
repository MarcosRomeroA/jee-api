<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Event\Application;

use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Application\Create\EventCreator;
use App\Contexts\Web\Event\Domain\Event;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\EventType;
use App\Contexts\Web\Event\Domain\Exception\InvalidEventTypeException;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Tests\Unit\Web\Event\Domain\EventMother;
use App\Tests\Unit\Web\Game\Domain\GameMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EventCreatorTest extends TestCase
{
    private EventRepository|MockObject $eventRepository;
    private GameRepository|MockObject $gameRepository;
    private ImageUploader|MockObject $imageUploader;
    private EventCreator $creator;

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->gameRepository = $this->createMock(GameRepository::class);
        $this->imageUploader = $this->createMock(ImageUploader::class);

        $this->creator = new EventCreator(
            $this->eventRepository,
            $this->gameRepository,
            $this->imageUploader,
        );
    }

    public function testItShouldCreateAnEvent(): void
    {
        $id = Uuid::random();
        $name = 'Championship 2025';
        $description = 'Annual championship';
        $type = 'virtual';
        $startAt = (new \DateTimeImmutable('+1 day'))->format(\DateTimeInterface::ATOM);
        $endAt = (new \DateTimeImmutable('+2 days'))->format(\DateTimeInterface::ATOM);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->imageUploader
            ->method('isBase64Image')
            ->willReturn(false);

        $this->eventRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Event $event) use ($id, $name, $description) {
                    return $event->getId()->equals($id) &&
                        $event->getName() === $name &&
                        $event->getDescription() === $description &&
                        $event->getType() === EventType::VIRTUAL;
                }),
            );

        $this->creator->createOrUpdate(
            $id,
            $name,
            $description,
            null,
            null,
            $type,
            $startAt,
            $endAt,
        );
    }

    public function testItShouldCreateAnEventWithGame(): void
    {
        $id = Uuid::random();
        $gameId = Uuid::random();
        $game = GameMother::create($gameId);
        $startAt = (new \DateTimeImmutable('+1 day'))->format(\DateTimeInterface::ATOM);
        $endAt = (new \DateTimeImmutable('+2 days'))->format(\DateTimeInterface::ATOM);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->gameRepository
            ->expects($this->once())
            ->method('findById')
            ->with($gameId)
            ->willReturn($game);

        $this->imageUploader
            ->method('isBase64Image')
            ->willReturn(false);

        $this->eventRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Event $event) use ($game) {
                    return $event->getGame() === $game;
                }),
            );

        $this->creator->createOrUpdate(
            $id,
            'Event Name',
            'Description',
            $gameId,
            null,
            'presencial',
            $startAt,
            $endAt,
        );
    }

    public function testItShouldUpdateAnExistingEvent(): void
    {
        $id = Uuid::random();
        $existingEvent = EventMother::create($id);
        $newName = 'Updated Event';
        $startAt = (new \DateTimeImmutable('+1 day'))->format(\DateTimeInterface::ATOM);
        $endAt = (new \DateTimeImmutable('+2 days'))->format(\DateTimeInterface::ATOM);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($existingEvent);

        $this->imageUploader
            ->method('isBase64Image')
            ->willReturn(false);

        $this->eventRepository
            ->expects($this->once())
            ->method('save')
            ->with($existingEvent);

        $this->creator->createOrUpdate(
            $id,
            $newName,
            'Updated description',
            null,
            null,
            'virtual',
            $startAt,
            $endAt,
        );

        $this->assertEquals($newName, $existingEvent->getName());
    }

    public function testItShouldThrowExceptionForInvalidEventType(): void
    {
        $id = Uuid::random();
        $startAt = (new \DateTimeImmutable('+1 day'))->format(\DateTimeInterface::ATOM);
        $endAt = (new \DateTimeImmutable('+2 days'))->format(\DateTimeInterface::ATOM);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->expectException(InvalidEventTypeException::class);

        $this->creator->createOrUpdate(
            $id,
            'Event Name',
            'Description',
            null,
            null,
            'invalid_type',
            $startAt,
            $endAt,
        );
    }

    public function testItShouldProcessBase64Image(): void
    {
        $id = Uuid::random();
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJ';
        $generatedFilename = 'generated-image.png';
        $startAt = (new \DateTimeImmutable('+1 day'))->format(\DateTimeInterface::ATOM);
        $endAt = (new \DateTimeImmutable('+2 days'))->format(\DateTimeInterface::ATOM);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->imageUploader
            ->expects($this->once())
            ->method('isBase64Image')
            ->with($base64Image)
            ->willReturn(true);

        $this->imageUploader
            ->expects($this->once())
            ->method('upload')
            ->with($base64Image, 'event/' . $id->value())
            ->willReturn($generatedFilename);

        $this->eventRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Event $event) use ($generatedFilename) {
                    return $event->getImage() === $generatedFilename;
                }),
            );

        $this->creator->createOrUpdate(
            $id,
            'Event Name',
            'Description',
            null,
            $base64Image,
            'virtual',
            $startAt,
            $endAt,
        );
    }
}
