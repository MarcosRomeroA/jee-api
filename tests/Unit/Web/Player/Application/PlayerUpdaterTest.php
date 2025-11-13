<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Application\Update\PlayerUpdater;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Tests\Unit\Web\Player\Domain\GameRankMother;
use App\Tests\Unit\Web\Player\Domain\GameRoleMother;
use App\Tests\Unit\Web\Player\Domain\PlayerMother;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class PlayerUpdaterTest extends TestCase
{
    private PlayerRepository|MockObject $repository;
    private EntityManagerInterface|MockObject $entityManager;
    private PlayerUpdater $updater;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PlayerRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->updater = new PlayerUpdater($this->repository, $this->entityManager);
    }

    public function testItShouldUpdateAPlayer(): void
    {
        $id = Uuid::random();
        $newUsername = 'UpdatedGamer456';
        $newGameRoleId1 = Uuid::random();
        $newGameRoleId2 = Uuid::random();
        $newGameRankId = Uuid::random();

        $player = PlayerMother::create($id);
        $gameRole1 = GameRoleMother::create($newGameRoleId1);
        $gameRole2 = GameRoleMother::create($newGameRoleId2);
        $gameRank = GameRankMother::create($newGameRankId);

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($player);

        $this->entityManager
            ->expects($this->exactly(3))
            ->method('getReference')
            ->willReturnOnConsecutiveCalls($gameRank, $gameRole1, $gameRole2);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($player);

        $this->updater->update($id, $newUsername, [$newGameRoleId1, $newGameRoleId2], $newGameRankId);
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

        $this->updater->update($id, 'NewUsername', [Uuid::random()], Uuid::random());
    }
}

