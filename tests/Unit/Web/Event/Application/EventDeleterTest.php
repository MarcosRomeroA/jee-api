<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Event\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Application\Delete\EventDeleter;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\Exception\EventNotFoundException;
use App\Tests\Unit\Web\Event\Domain\EventMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EventDeleterTest extends TestCase
{
    private EventRepository|MockObject $eventRepository;
    private EventDeleter $deleter;

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->deleter = new EventDeleter($this->eventRepository);
    }

    public function testItShouldDeleteAnEvent(): void
    {
        $id = Uuid::random();
        $event = EventMother::create($id);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($event);

        $this->eventRepository
            ->expects($this->once())
            ->method('delete')
            ->with($event);

        $this->deleter->__invoke($id);
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

        $this->deleter->__invoke($id);
    }
}
