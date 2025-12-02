<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Application;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameAccountRequirementRepository;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Game\Domain\GameRoleRepository;
use App\Contexts\Web\Player\Application\Create\PlayerCreator;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\GameAccountDataValue;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Player\Domain\GameMother;
use App\Tests\Unit\Web\Player\Domain\GameRoleMother;
use App\Tests\Unit\Web\Player\Domain\UserMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class PlayerCreatorTest extends TestCase
{
    private PlayerRepository|MockObject $playerRepository;
    private UserRepository|MockObject $userRepository;
    private GameRepository|MockObject $gameRepository;
    private GameRoleRepository|MockObject $gameRoleRepository;
    private GameAccountRequirementRepository|MockObject $gameAccountRequirementRepository;
    private EventBus|MockObject $eventBus;
    private PlayerCreator $creator;

    protected function setUp(): void
    {
        $this->playerRepository = $this->createMock(PlayerRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->gameRepository = $this->createMock(GameRepository::class);
        $this->gameRoleRepository = $this->createMock(
            GameRoleRepository::class,
        );
        $this->gameAccountRequirementRepository = $this->createMock(
            GameAccountRequirementRepository::class,
        );
        $this->eventBus = $this->createMock(EventBus::class);

        $this->creator = new PlayerCreator(
            $this->playerRepository,
            $this->userRepository,
            $this->gameRepository,
            $this->gameRoleRepository,
            $this->gameAccountRequirementRepository,
            $this->eventBus,
        );
    }

    public function testItShouldCreateAPlayer(): void
    {
        $id = Uuid::random();
        $userId = Uuid::random();
        $gameId = Uuid::random();
        $gameRoleId = Uuid::random();
        $accountData = new GameAccountDataValue([
            'region' => 'las',
            'username' => 'RiotPlayer',
            'tag' => '1234',
        ]);

        $user = UserMother::create($userId);
        $game = GameMother::create($gameId);
        $gameRole = GameRoleMother::create($gameRoleId);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($userId)
            ->willReturn($user);

        $this->gameRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameId)
            ->willReturn($game);

        $this->gameRoleRepository
            ->expects($this->once())
            ->method("findById")
            ->with($this->callback(function (Uuid $uuid) use ($gameRoleId) {
                return $uuid->equals($gameRoleId);
            }))
            ->willReturn($gameRole);

        $this->gameAccountRequirementRepository
            ->expects($this->once())
            ->method("findByGameId")
            ->willReturn(null);

        $this->playerRepository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new \Exception("Player not found"));

        $this->playerRepository
            ->expects($this->once())
            ->method("existsByUserIdAndGameId")
            ->willReturn(false);

        $this->playerRepository
            ->expects($this->once())
            ->method("countByUserId")
            ->with($userId)
            ->willReturn(0);

        $this->playerRepository
            ->expects($this->once())
            ->method("save")
            ->with(
                $this->callback(function (Player $player) use ($id) {
                    return $player->id()->equals($id) &&
                        $player->username() === 'RiotPlayer' &&
                        $player->verified() === false;
                }),
            );

        $this->creator->create(
            $id,
            $userId,
            $gameId,
            [$gameRoleId->value()],
            $accountData,
        );
    }

    public function testItShouldThrowExceptionWhenUserNotFound(): void
    {
        $id = Uuid::random();
        $userId = Uuid::random();
        $gameId = Uuid::random();
        $gameRoleId = Uuid::random();
        $accountData = new GameAccountDataValue([
            'region' => 'las',
            'username' => 'RiotPlayer',
            'tag' => '1234',
        ]);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($userId)
            ->willThrowException(
                new \App\Contexts\Web\User\Domain\Exception\UserNotFoundException(
                    $userId->value(),
                ),
            );

        $this->expectException(UserNotFoundException::class);

        $this->creator->create(
            $id,
            $userId,
            $gameId,
            [$gameRoleId->value()],
            $accountData,
        );
    }

    public function testItShouldCreateAPlayerWithEmptyRoles(): void
    {
        $id = Uuid::random();
        $userId = Uuid::random();
        $gameId = Uuid::random();
        $accountData = new GameAccountDataValue([
            'region' => 'las',
            'username' => 'RiotPlayer',
            'tag' => '1234',
        ]);

        $user = UserMother::create($userId);
        $game = GameMother::create($gameId);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($userId)
            ->willReturn($user);

        $this->gameRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameId)
            ->willReturn($game);

        $this->gameRoleRepository
            ->expects($this->never())
            ->method("findById");

        $this->gameAccountRequirementRepository
            ->expects($this->once())
            ->method("findByGameId")
            ->willReturn(null);

        $this->playerRepository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new \Exception("Player not found"));

        $this->playerRepository
            ->expects($this->once())
            ->method("existsByUserIdAndGameId")
            ->willReturn(false);

        $this->playerRepository
            ->expects($this->once())
            ->method("countByUserId")
            ->with($userId)
            ->willReturn(0);

        $this->playerRepository
            ->expects($this->once())
            ->method("save")
            ->with(
                $this->callback(function (Player $player) use ($id) {
                    return $player->id()->equals($id) &&
                        $player->username() === 'RiotPlayer' &&
                        $player->gameRoles()->isEmpty() &&
                        $player->verified() === false;
                }),
            );

        $this->creator->create(
            $id,
            $userId,
            $gameId,
            [], // Empty roles array
            $accountData,
        );
    }
}
