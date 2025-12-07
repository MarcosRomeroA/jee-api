<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Event\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Application\Find\EventFinder;
use App\Contexts\Web\Event\Application\Shared\EventResponse;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\Exception\EventNotFoundException;
use App\Tests\Unit\Web\Event\Domain\EventMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EventFinderTest extends TestCase
{
    private const CDN_BASE_URL = 'https://cdn.example.com';

    private EventRepository|MockObject $eventRepository;
    private EventFinder $finder;

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->finder = new EventFinder($this->eventRepository, self::CDN_BASE_URL);
    }

    public function testItShouldFindAnEvent(): void
    {
        $id = Uuid::random();
        $event = EventMother::create($id, 'Test Event');

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($event);

        $response = $this->finder->__invoke($id);

        $this->assertInstanceOf(EventResponse::class, $response);
        $this->assertEquals($id->value(), $response->id);
        $this->assertEquals('Test Event', $response->name);
    }

    public function testItShouldThrowExceptionWhenEventNotFound(): void
    {
        $id = Uuid::random();

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->expectException(EventNotFoundException::class);

        $this->finder->__invoke($id);
    }

    public function testItShouldGenerateImageUrl(): void
    {
        $id = Uuid::random();
        $event = EventMother::create($id, 'Test Event', image: 'event-image.jpg');

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($event);

        $response = $this->finder->__invoke($id);

        $expectedUrl = self::CDN_BASE_URL . '/jee/event/' . $id->value() . '/event-image.jpg';
        $this->assertEquals($expectedUrl, $response->image);
    }
}
