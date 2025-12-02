<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameAccountRequirementRepository;
use App\Contexts\Web\Player\Application\Update\PlayerUpdater;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\GameAccountDataValue;
use App\Tests\Unit\Web\Player\Domain\GameRoleMother;
use App\Tests\Unit\Web\Player\Domain\PlayerMother;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class PlayerUpdaterTest extends TestCase
{
    private PlayerRepository|MockObject $repository;
    private EntityManagerInterface|MockObject $entityManager;
    private GameAccountRequirementRepository|MockObject $gameAccountRequirementRepository;
    private PlayerUpdater $updater;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PlayerRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->gameAccountRequirementRepository = $this->createMock(
            GameAccountRequirementRepository::class,
        );
        $this->updater = new PlayerUpdater(
            $this->repository,
            $this->entityManager,
            $this->gameAccountRequirementRepository,
        );
    }

    public function testItShouldUpdateAPlayer(): void
    {
        $id = Uuid::random();
        $newGameRoleId = Uuid::random();
        $newAccountData = new GameAccountDataValue([
            'region' => 'las',
            'username' => 'UpdatedRiot',
            'tag' => '5678',
        ]);

        $player = PlayerMother::create($id);
        $gameRole = GameRoleMother::create($newGameRoleId);

        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willReturn($player);

        $this->entityManager
            ->expects($this->once())
            ->method("getReference")
            ->willReturn($gameRole);

        $this->gameAccountRequirementRepository
            ->expects($this->once())
            ->method("findByGameId")
            ->willReturn(null);

        $this->repository
            ->expects($this->once())
            ->method("save")
            ->with($player);

        $this->updater->update(
            $id,
            [$newGameRoleId->value()],
            $newAccountData,
        );
    }

    public function testItShouldThrowExceptionWhenPlayerNotFound(): void
    {
        $id = Uuid::random();
        $gameRoleId = Uuid::random();
        $accountData = new GameAccountDataValue([
            'region' => 'las',
            'username' => 'RiotPlayer',
            'tag' => '1234',
        ]);

        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new PlayerNotFoundException($id->value()));

        $this->expectException(PlayerNotFoundException::class);

        $this->updater->update(
            $id,
            [$gameRoleId->value()],
            $accountData,
        );
    }
}
