<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\User\Application;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Web\User\Application\Search\UserSearcher;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\User\Domain\UserMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class UserSearcherTest extends TestCase
{
    private UserRepository|MockObject $repository;
    private FileManager|MockObject $fileManager;
    private UserSearcher $searcher;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->fileManager = $this->createMock(FileManager::class);
        $this->searcher = new UserSearcher($this->repository, $this->fileManager);
    }

    public function testItShouldSearchUsersWithoutQuery(): void
    {
        $users = [
            UserMother::create(username: 'user1'),
            UserMother::create(username: 'user2'),
            UserMother::create(username: 'user3'),
        ];

        $criteria = ['offset' => 0, 'limit' => 20];

        $this->repository
            ->expects($this->once())
            ->method('searchByCriteria')
            ->with($criteria)
            ->willReturn($users);

        $this->repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($criteria)
            ->willReturn(3);

        $this->fileManager
            ->expects($this->exactly(3))
            ->method('generateTemporaryUrl')
            ->willReturn('https://example.com/profile.jpg');

        $response = $this->searcher->__invoke($criteria);

        $this->assertCount(3, $response->users);
        $this->assertEquals(3, $response->total);
        $this->assertEquals(0, $response->offset);
        $this->assertEquals(20, $response->limit);
    }

    public function testItShouldSearchUsersWithQuery(): void
    {
        $users = [
            UserMother::create(username: 'johndoe'),
        ];

        $criteria = ['q' => 'john', 'offset' => 0, 'limit' => 20];

        $this->repository
            ->expects($this->once())
            ->method('searchByCriteria')
            ->with($criteria)
            ->willReturn($users);

        $this->repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($criteria)
            ->willReturn(1);

        $this->fileManager
            ->expects($this->once())
            ->method('generateTemporaryUrl')
            ->willReturn('https://example.com/profile.jpg');

        $response = $this->searcher->__invoke($criteria);

        $this->assertCount(1, $response->users);
        $this->assertEquals(1, $response->total);
    }

    public function testItShouldReturnEmptyResultWhenNoUsersFound(): void
    {
        $criteria = ['q' => 'nonexistent', 'offset' => 0, 'limit' => 20];

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

        $response = $this->searcher->__invoke($criteria);

        $this->assertCount(0, $response->users);
        $this->assertEquals(0, $response->total);
    }
}

