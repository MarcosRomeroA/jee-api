<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Notification\Application;

use App\Contexts\Web\Notification\Application\Search\NotificationSearcher;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Tests\Unit\Web\Notification\Domain\NotificationMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class NotificationSearcherTest extends TestCase
{
    private const CDN_BASE_URL = 'https://cdn.example.com';

    private NotificationRepository|MockObject $repository;
    private NotificationSearcher $searcher;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(NotificationRepository::class);
        $this->searcher = new NotificationSearcher($this->repository, self::CDN_BASE_URL);
    }

    public function testItShouldSearchNotificationsWithCriteria(): void
    {
        // Arrange
        $criteria = ['userId' => 'user-123', 'limit' => 20, 'offset' => 0];

        $notifications = [
            NotificationMother::random(),
            NotificationMother::random(),
            NotificationMother::random(),
        ];

        $this->repository
            ->expects($this->once())
            ->method('searchByCriteria')
            ->with($criteria)
            ->willReturn($notifications);

        $this->repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($criteria)
            ->willReturn(3);

        // Act
        $response = ($this->searcher)($criteria);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldReturnEmptyListWhenNoNotifications(): void
    {
        // Arrange
        $criteria = ['userId' => 'user-456', 'limit' => 20, 'offset' => 0];

        $this->repository
            ->expects($this->once())
            ->method('searchByCriteria')
            ->with($criteria)
            ->willReturn([]);

        $this->repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($criteria)
            ->willReturn(0);

        // Act
        $response = ($this->searcher)($criteria);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldSearchWithEmptyCriteria(): void
    {
        // Arrange
        $criteria = ['limit' => 20, 'offset' => 0];
        $notifications = [
            NotificationMother::random(),
            NotificationMother::random(),
        ];

        $this->repository
            ->expects($this->once())
            ->method('searchByCriteria')
            ->with($criteria)
            ->willReturn($notifications);

        $this->repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($criteria)
            ->willReturn(2);

        // Act
        $response = ($this->searcher)($criteria);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldHandlePaginationCriteria(): void
    {
        // Arrange
        $criteria = [
            'userId' => 'user-789',
            'limit' => 10,
            'offset' => 20, // Page 3
            'read' => false
        ];

        $notifications = [
            NotificationMother::random(),
            NotificationMother::random(),
        ];

        $this->repository
            ->expects($this->once())
            ->method('searchByCriteria')
            ->with($criteria)
            ->willReturn($notifications);

        $this->repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($criteria)
            ->willReturn(50); // Total unread notifications

        // Act
        $response = ($this->searcher)($criteria);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldSearchOnlyUnreadNotifications(): void
    {
        // Arrange
        $criteria = [
            'userId' => 'user-999',
            'read' => false,
            'limit' => 20,
            'offset' => 0
        ];

        $notifications = [
            NotificationMother::random(),
        ];

        $this->repository
            ->expects($this->once())
            ->method('searchByCriteria')
            ->with($criteria)
            ->willReturn($notifications);

        $this->repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($criteria)
            ->willReturn(1);

        // Act
        $response = ($this->searcher)($criteria);

        // Assert
        $this->assertNotNull($response);
    }
}
