<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Application\Create\PlayerCreator;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Player\Domain\GameRankMother;
use App\Tests\Unit\Web\Player\Domain\GameRoleMother;
use App\Tests\Unit\Web\Player\Domain\UserMother;
use App\Tests\Unit\Web\Player\Infrastructure\RankVerifierServiceStub;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class PlayerCreatorTest extends TestCase
{
    private PlayerRepository|MockObject $playerRepository;
    private UserRepository|MockObject $userRepository;
    private EntityManagerInterface|MockObject $entityManager;
    private PlayerCreator $creator;

    protected function setUp(): void
    {
        $this->playerRepository = $this->createMock(PlayerRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Usar stub en lugar de mock porque RankVerifierService es final
        $this->creator = new PlayerCreator(
            $this->playerRepository,
            $this->userRepository,
            $this->entityManager,
            new RankVerifierServiceStub()
        );
    }

    public function testItShouldCreateAPlayer(): void
    {
        $id = Uuid::random();
        $userId = Uuid::random();
        $gameId = Uuid::random();
        $gameRoleId1 = Uuid::random();
        $gameRoleId2 = Uuid::random();
        $gameRankId = Uuid::random();
        $username = new UsernameValue('ProGamer123');

        $user = UserMother::create($userId);
        $gameRole1 = GameRoleMother::create($gameRoleId1);
        $gameRole2 = GameRoleMother::create($gameRoleId2);
        $gameRank = GameRankMother::create($gameRankId);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->exactly(3))
            ->method('getReference')
            ->willReturnOnConsecutiveCalls($gameRank, $gameRole1, $gameRole2);

        $this->playerRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Player $player) use ($id, $username) {
                return $player->id()->equals($id)
                    && $player->username()->value() === $username->value()
                    && $player->verified() === false
                    && count($player->playerRoles()) === 2;
            }));

        $this->creator->create($id, $userId, $gameId, [$gameRoleId1, $gameRoleId2], $gameRankId, $username);
    }

    public function testItShouldThrowExceptionWhenUserNotFound(): void
    {
        $id = Uuid::random();
        $userId = Uuid::random();
        $gameId = Uuid::random();
        $gameRoleId = Uuid::random();
        $gameRankId = Uuid::random();
        $username = new UsernameValue('ProGamer123');

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willThrowException(new \App\Contexts\Web\User\Domain\Exception\UserNotFoundException());

        $this->expectException(UserNotFoundException::class);

        $this->creator->create($id, $userId, $gameId, [$gameRoleId], $gameRankId, $username);
    }
}

