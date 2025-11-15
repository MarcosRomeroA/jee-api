<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Game\Domain\GameRoleRepository;
use App\Contexts\Web\Player\Application\Create\PlayerCreator;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Player\Domain\GameRankMother;
use App\Tests\Unit\Web\Player\Domain\GameRoleMother;
use App\Tests\Unit\Web\Player\Domain\UserMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class PlayerCreatorTest extends TestCase
{
    private PlayerRepository|MockObject $playerRepository;
    private UserRepository|MockObject $userRepository;
    private GameRoleRepository|MockObject $gameRoleRepository;
    private GameRankRepository|MockObject $gameRankRepository;
    private PlayerCreator $creator;

    protected function setUp(): void
    {
        $this->playerRepository = $this->createMock(PlayerRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->gameRoleRepository = $this->createMock(
            GameRoleRepository::class,
        );
        $this->gameRankRepository = $this->createMock(
            GameRankRepository::class,
        );

        $this->creator = new PlayerCreator(
            $this->playerRepository,
            $this->userRepository,
            $this->gameRoleRepository,
            $this->gameRankRepository,
        );
    }

    public function testItShouldCreateAPlayer(): void
    {
        $id = Uuid::random();
        $userId = Uuid::random();
        $gameRoleId = Uuid::random();
        $gameRankId = Uuid::random();
        $username = new UsernameValue("ProGamer123");

        $user = UserMother::create($userId);
        $gameRole = GameRoleMother::create($gameRoleId);
        $gameRank = GameRankMother::create($gameRankId);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($userId)
            ->willReturn($user);

        $this->gameRoleRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameRoleId)
            ->willReturn($gameRole);

        $this->gameRankRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameRankId)
            ->willReturn($gameRank);

        $this->playerRepository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new \Exception("Player not found"));

        $this->playerRepository
            ->expects($this->once())
            ->method("existsByUserIdAndUsernameAndGameId")
            ->willReturn(false);

        $this->playerRepository
            ->expects($this->once())
            ->method("save")
            ->with(
                $this->callback(function (Player $player) use ($id, $username) {
                    return $player->id()->equals($id) &&
                        $player->username()->value() === $username->value() &&
                        $player->verified() === false;
                }),
            );

        $this->creator->create(
            $id,
            $userId,
            $gameRoleId,
            $gameRankId,
            $username,
        );
    }

    public function testItShouldThrowExceptionWhenUserNotFound(): void
    {
        $id = Uuid::random();
        $userId = Uuid::random();
        $gameRoleId = Uuid::random();
        $gameRankId = Uuid::random();
        $username = new UsernameValue("ProGamer123");

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
            $gameRoleId,
            $gameRankId,
            $username,
        );
    }
}
