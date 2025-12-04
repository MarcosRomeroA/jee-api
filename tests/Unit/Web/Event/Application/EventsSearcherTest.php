<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Event\Application;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Application\Search\EventsSearcher;
use App\Contexts\Web\Event\Application\Shared\EventCollectionResponse;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\EventType;
use App\Tests\Unit\Web\Event\Domain\EventMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EventsSearcherTest extends TestCase
{
    private EventRepository|MockObject $eventRepository;
    private FileManager|MockObject $fileManager;
    private EventsSearcher $searcher;

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->fileManager = $this->createMock(FileManager::class);
        $this->searcher = new EventsSearcher($this->eventRepository, $this->fileManager);
    }

    public function testItShouldSearchUpcomingEvents(): void
    {
        $events = [
            EventMother::upcoming(),
            EventMother::upcoming(),
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('searchUpcoming')
            ->with(null, null, 10, 0)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('countUpcoming')
            ->with(null, null)
            ->willReturn(2);

        $response = $this->searcher->__invoke(null, null, 10, 0);

        $this->assertInstanceOf(EventCollectionResponse::class, $response);
        $this->assertCount(2, $response->events);
        $this->assertEquals(2, $response->total);
    }

    public function testItShouldFilterByGameId(): void
    {
        $gameId = Uuid::random();
        $events = [EventMother::upcoming()];

        $this->eventRepository
            ->expects($this->once())
            ->method('searchUpcoming')
            ->with($gameId, null, 10, 0)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('countUpcoming')
            ->with($gameId, null)
            ->willReturn(1);

        $response = $this->searcher->__invoke($gameId, null, 10, 0);

        $this->assertCount(1, $response->events);
    }

    public function testItShouldFilterByType(): void
    {
        $type = EventType::PRESENCIAL;
        $events = [EventMother::presencial()];

        $this->eventRepository
            ->expects($this->once())
            ->method('searchUpcoming')
            ->with(null, $type, 10, 0)
            ->willReturn($events);

        $this->eventRepository
            ->expects($this->once())
            ->method('countUpcoming')
            ->with(null, $type)
            ->willReturn(1);

        $response = $this->searcher->__invoke(null, $type, 10, 0);

        $this->assertCount(1, $response->events);
        $this->assertEquals('presencial', $response->events[0]->type);
    }

    public function testItShouldReturnEmptyCollectionWhenNoEvents(): void
    {
        $this->eventRepository
            ->expects($this->once())
            ->method('searchUpcoming')
            ->willReturn([]);

        $this->eventRepository
            ->expects($this->once())
            ->method('countUpcoming')
            ->willReturn(0);

        $response = $this->searcher->__invoke(null, null, 10, 0);

        $this->assertCount(0, $response->events);
        $this->assertEquals(0, $response->total);
    }

    public function testItShouldApplyPagination(): void
    {
        $limit = 5;
        $offset = 10;

        $this->eventRepository
            ->expects($this->once())
            ->method('searchUpcoming')
            ->with(null, null, $limit, $offset)
            ->willReturn([]);

        $this->eventRepository
            ->expects($this->once())
            ->method('countUpcoming')
            ->willReturn(0);

        $response = $this->searcher->__invoke(null, null, $limit, $offset);

        $this->assertEquals($limit, $response->limit);
        $this->assertEquals($offset, $response->offset);
    }
}
