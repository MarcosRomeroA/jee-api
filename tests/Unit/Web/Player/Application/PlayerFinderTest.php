<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Application\Find\PlayerFinder;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Tests\Unit\Web\Player\Domain\PlayerMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class PlayerFinderTest extends TestCase
{
    private PlayerRepository|MockObject $repository;
    private PlayerFinder $finder;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PlayerRepository::class);
        $this->finder = new PlayerFinder($this->repository);
    }

    public function testItShouldFindAPlayer(): void
    {
        $id = Uuid::random();
        $player = PlayerMother::create(id: $id, username: 'TestPlayer');

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($player);

        $response = $this->finder->find($id);

        $this->assertEquals($id->value(), $response->id());
        $this->assertEquals('TestPlayer', $response->username());
        $this->assertFalse($response->verified());
    }

    public function testItShouldThrowExceptionWhenPlayerNotFound(): void
    {
        $id = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->expectException(PlayerNotFoundException::class);

        $this->finder->find($id);
    }
}

