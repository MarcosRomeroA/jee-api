<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamGame;
use PHPUnit\Framework\TestCase;

final class TeamGameTest extends TestCase
{
    public function testItShouldCreateTeamGame(): void
    {
        $id = Uuid::random();
        $team = TeamMother::create();
        $game = GameMother::random();

        $teamGame = new TeamGame($id, $team, $game);

        $this->assertEquals($id, $teamGame->getId());
        $this->assertEquals($team, $teamGame->getTeam());
        $this->assertEquals($game, $teamGame->getGame());
        $this->assertInstanceOf(\DateTimeImmutable::class, $teamGame->getAddedAt());
    }

    public function testTeamShouldAddGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $this->assertEquals(0, $team->getGamesQuantity());

        $team->addGame($game);

        $this->assertEquals(1, $team->getGamesQuantity());
        $this->assertTrue($team->hasGame($game));
    }

    public function testTeamShouldNotAddDuplicateGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $team->addGame($game);
        $team->addGame($game); // Try to add same game again

        $this->assertEquals(1, $team->getGamesQuantity());
    }

    public function testTeamShouldRemoveGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $team->addGame($game);
        $this->assertEquals(1, $team->getGamesQuantity());

        $team->removeGame($game);

        $this->assertEquals(0, $team->getGamesQuantity());
        $this->assertFalse($team->hasGame($game));
    }

    public function testTeamShouldReturnFalseWhenGameNotFound(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $this->assertFalse($team->hasGame($game));
    }

    public function testTeamShouldHandleMultipleGames(): void
    {
        $team = TeamMother::create();
        $game1 = GameMother::random();
        $game2 = GameMother::random();

        $team->addGame($game1);
        $team->addGame($game2);

        $this->assertEquals(2, $team->getGamesQuantity());
        $this->assertTrue($team->hasGame($game1));
        $this->assertTrue($team->hasGame($game2));
    }
}
