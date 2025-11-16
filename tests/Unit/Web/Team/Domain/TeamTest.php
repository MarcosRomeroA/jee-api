<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\ValueObject\TeamNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamImageValue;
use PHPUnit\Framework\TestCase;

final class TeamTest extends TestCase
{
    public function testItShouldCreateATeam(): void
    {
        $id = Uuid::random();
        $name = "Los Campeones";
        $description = "A professional gaming team";
        $image = "https://example.com/team.jpg";
        $creator = UserMother::random();

        $team = Team::create(
            $id,
            new TeamNameValue($name),
            new TeamDescriptionValue($description),
            new TeamImageValue($image),
            $creator
        );

        $this->assertEquals($id, $team->id());
        $this->assertEquals($name, $team->name());
        $this->assertEquals($description, $team->description());
        $this->assertEquals($image, $team->image());
        $this->assertEquals(0, $team->playersQuantity());
        $this->assertEquals(0, $team->gamesQuantity());
        $this->assertEquals($creator, $team->creator());
        $this->assertEquals($creator, $team->leader());
    }

    public function testItShouldUpdateTeam(): void
    {
        $team = TeamMother::create();
        $newName = "Updated Team Name";
        $newDescription = "Updated team description";
        $newImage = "https://example.com/new-image.jpg";

        $team->update(
            new TeamNameValue($newName),
            new TeamDescriptionValue($newDescription),
            new TeamImageValue($newImage)
        );

        $this->assertEquals($newName, $team->name());
        $this->assertEquals($newDescription, $team->description());
        $this->assertEquals($newImage, $team->image());
    }

    public function testItShouldSetLeader(): void
    {
        $team = TeamMother::create();
        $leaderId = Uuid::random();
        $leader = UserMother::create($leaderId);

        $team->setLeader($leader);

        $this->assertEquals($leader, $team->leader());
        $this->assertTrue($team->isLeader($leaderId));
    }

    public function testItShouldReturnPlayersQuantity(): void
    {
        $team = TeamMother::create();

        $this->assertEquals(0, $team->playersQuantity());
    }

    public function testItShouldAddGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $team->addGame($game);

        $this->assertEquals(1, $team->gamesQuantity());
        $this->assertTrue($team->hasGame($game));
    }

    public function testItShouldNotAddDuplicateGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $team->addGame($game);
        $team->addGame($game);

        $this->assertEquals(1, $team->gamesQuantity());
    }

    public function testItShouldRemoveGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $team->addGame($game);
        $this->assertEquals(1, $team->gamesQuantity());

        $team->removeGame($game);
        $this->assertEquals(0, $team->gamesQuantity());
        $this->assertFalse($team->hasGame($game));
    }
}
