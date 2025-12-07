<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\User\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Find\UserFinder;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\User\Domain\UserMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class UserFinderTest extends TestCase
{
    private const CDN_BASE_URL = 'https://cdn.example.com';

    private UserRepository|MockObject $repository;
    private UserFinder $finder;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->finder = new UserFinder($this->repository, self::CDN_BASE_URL);
    }

    public function testItShouldFindAUserById(): void
    {
        $id = Uuid::random();
        $user = UserMother::create(id: $id);

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($user);

        $response = $this->finder->__invoke($id);

        $this->assertEquals($id->value(), $response->id);
        $this->assertNotNull($response->username);
        $this->assertNotNull($response->email);
    }

    public function testItShouldThrowExceptionWhenUserNotFound(): void
    {
        $id = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willThrowException(new UserNotFoundException($id->value()));

        $this->expectException(UserNotFoundException::class);

        $this->finder->__invoke($id);
    }
}
