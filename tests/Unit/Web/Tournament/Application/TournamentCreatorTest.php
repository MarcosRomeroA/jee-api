<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Tournament\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Tournament\Application\Create\TournamentCreator;
use App\Contexts\Web\Tournament\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentStatusRepository;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Tournament\Domain\GameMother;
use App\Tests\Unit\Web\Tournament\Domain\TournamentStatusMother;
use App\Tests\Unit\Web\Tournament\Domain\UserMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class TournamentCreatorTest extends TestCase
{
    private TournamentRepository|MockObject $tournamentRepository;
    private GameRepository|MockObject $gameRepository;
    private UserRepository|MockObject $userRepository;
    private TournamentStatusRepository|MockObject $statusRepository;
    private GameRankRepository|MockObject $gameRankRepository;
    private TournamentCreator $creator;

    protected function setUp(): void
    {
        $this->tournamentRepository = $this->createMock(
            TournamentRepository::class,
        );
        $this->gameRepository = $this->createMock(GameRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->statusRepository = $this->createMock(
            TournamentStatusRepository::class,
        );
        $this->gameRankRepository = $this->createMock(
            GameRankRepository::class,
        );

        $this->creator = new TournamentCreator(
            $this->tournamentRepository,
            $this->gameRepository,
            $this->userRepository,
            $this->statusRepository,
            $this->gameRankRepository,
        );
    }

    public function testItShouldCreateATournament(): void
    {
        $id = Uuid::random();
        $gameId = Uuid::random();
        $responsibleId = Uuid::random();
        $name = "Championship 2025";
        $maxTeams = 16;
        $startAt = new \DateTimeImmutable("+1 day");
        $endAt = new \DateTimeImmutable("+7 days");

        $game = GameMother::create($gameId);
        $responsible = UserMother::create($responsibleId);
        $status = TournamentStatusMother::created();

        $this->gameRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameId)
            ->willReturn($game);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($responsibleId)
            ->willReturn($responsible);

        $this->statusRepository
            ->expects($this->once())
            ->method("findByName")
            ->with("created")
            ->willReturn($status);

        $this->tournamentRepository
            ->expects($this->once())
            ->method("save")
            ->with(
                $this->callback(function (Tournament $tournament) use (
                    $id,
                    $name,
                ) {
                    return $tournament->id()->equals($id) &&
                        $tournament->name() === $name;
                }),
            );

        $this->creator->create(
            $id,
            $gameId,
            $name,
            false,
            $responsibleId,
            "Tournament description",
            $maxTeams,
            null,
            '$1000 USD',
            "NA",
            $startAt,
            $endAt,
        );
    }

    public function testItShouldThrowExceptionWhenGameNotFound(): void
    {
        $id = Uuid::random();
        $gameId = Uuid::random();
        $responsibleId = Uuid::random();

        $this->gameRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameId)
            ->willThrowException(new GameNotFoundException($gameId->value()));

        $this->expectException(GameNotFoundException::class);

        $this->creator->create(
            $id,
            $gameId,
            "Tournament Name",
            false,
            $responsibleId,
            null,
            16,
            null,
            null,
            null,
            new \DateTimeImmutable("+1 day"),
            new \DateTimeImmutable("+7 days"),
        );
    }

    public function testItShouldThrowExceptionWhenUserNotFound(): void
    {
        $id = Uuid::random();
        $gameId = Uuid::random();
        $responsibleId = Uuid::random();

        $game = GameMother::create($gameId);

        $this->gameRepository
            ->expects($this->once())
            ->method("findById")
            ->with($gameId)
            ->willReturn($game);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($responsibleId)
            ->willThrowException(new UserNotFoundException());

        $this->expectException(UserNotFoundException::class);

        $this->creator->create(
            $id,
            $gameId,
            "Tournament Name",
            false,
            $responsibleId,
            null,
            16,
            null,
            null,
            null,
            new \DateTimeImmutable("+1 day"),
            new \DateTimeImmutable("+7 days"),
        );
    }
}
