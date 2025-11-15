<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Team\Application\Create\TeamCreator;
use App\Contexts\Web\Team\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Team\Domain\GameMother;
use App\Tests\Unit\Web\Team\Domain\UserMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class TeamCreatorTest extends TestCase
{
    private TeamRepository|MockObject $teamRepository;
    private UserRepository|MockObject $userRepository;
    private GameRepository|MockObject $gameRepository;
    private TeamCreator $creator;

    protected function setUp(): void
    {
        $this->teamRepository = $this->createMock(TeamRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->gameRepository = $this->createMock(GameRepository::class);

        $this->creator = new TeamCreator(
            $this->teamRepository,
            $this->userRepository,
            $this->gameRepository,
        );
    }

    public function testItShouldCreateATeam(): void
    {
        $id = Uuid::random();
        $gameId = Uuid::random();
        $ownerId = Uuid::random();
        $name = "Los Campeones";
        $image = "https://example.com/team.jpg";

        $game = GameMother::create($gameId);
        $owner = UserMother::create($ownerId);

        $this->gameRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameId)
            ->willReturn($game);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($ownerId)
            ->willReturn($owner);

        $this->teamRepository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new TeamNotFoundException($id->value()));

        $this->teamRepository
            ->expects($this->once())
            ->method("save")
            ->with(
                $this->callback(function (Team $team) use ($id, $name) {
                    return $team->id()->equals($id) && $team->name() === $name;
                }),
            );

        $this->creator->create($id, $gameId, $ownerId, $name, $image);
    }

    public function testItShouldThrowExceptionWhenGameNotFound(): void
    {
        $id = Uuid::random();
        $gameId = Uuid::random();
        $ownerId = Uuid::random();

        $this->gameRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameId)
            ->willThrowException(new GameNotFoundException($gameId->value()));

        $this->expectException(GameNotFoundException::class);

        $this->creator->create($id, $gameId, $ownerId, "Team Name", null);
    }

    public function testItShouldThrowExceptionWhenUserNotFound(): void
    {
        $id = Uuid::random();
        $gameId = Uuid::random();
        $ownerId = Uuid::random();

        $game = GameMother::create($gameId);

        $this->gameRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameId)
            ->willReturn($game);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($ownerId)
            ->willThrowException(new UserNotFoundException($ownerId->value()));

        $this->expectException(UserNotFoundException::class);

        $this->creator->create($id, $gameId, $ownerId, "Team Name", null);
    }
}
