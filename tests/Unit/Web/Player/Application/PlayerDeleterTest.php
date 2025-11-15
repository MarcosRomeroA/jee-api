<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Application\Delete\PlayerDeleter;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Tests\Unit\Web\Player\Domain\PlayerMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class PlayerDeleterTest extends TestCase
{
    private PlayerRepository|MockObject $repository;
    private PlayerDeleter $deleter;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PlayerRepository::class);
        $this->deleter = new PlayerDeleter($this->repository);
    }

    public function testItShouldDeleteAPlayer(): void
    {
        $id = Uuid::random();
        $player = PlayerMother::create($id);

        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willReturn($player);

        $this->repository
            ->expects($this->once())
            ->method("delete")
            ->with($player);

        $this->deleter->delete($id);
    }

    public function testItShouldThrowExceptionWhenPlayerNotFound(): void
    {
        $id = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new PlayerNotFoundException($id->value()));

        $this->expectException(PlayerNotFoundException::class);

        $this->deleter->delete($id);
    }
}
