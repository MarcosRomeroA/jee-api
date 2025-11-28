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

        $this->assertEquals($id, $team->getId());
        $this->assertEquals($name, $team->getName());
        $this->assertEquals($description, $team->getDescription());
        $this->assertEquals($image, $team->getImage());
        $this->assertEquals(0, $team->getPlayersQuantity());
        $this->assertEquals(0, $team->getGamesQuantity());
        $this->assertEquals(1, $team->getUsersQuantity()); // Creator is automatically added as TeamUser
        $this->assertEquals($creator, $team->getCreator());
        $this->assertEquals($creator, $team->getLeader());
        $this->assertTrue($team->isOwner($creator->getId()));
        $this->assertTrue($team->isLeader($creator->getId()));
        $this->assertTrue($team->isMember($creator->getId()));
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

        $this->assertEquals($newName, $team->getName());
        $this->assertEquals($newDescription, $team->getDescription());
        $this->assertEquals($newImage, $team->getImage());
    }

    public function testItShouldSetLeader(): void
    {
        $team = TeamMother::create();
        $leaderId = Uuid::random();
        $leader = UserMother::create($leaderId);

        $team->setLeader($leader);

        $this->assertEquals($leader, $team->getLeader());
        $this->assertTrue($team->isLeader($leaderId));
    }

    public function testItShouldReturnPlayersQuantity(): void
    {
        $team = TeamMother::create();

        $this->assertEquals(0, $team->getPlayersQuantity());
    }

    public function testItShouldAddGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $team->addGame($game);

        $this->assertEquals(1, $team->getGamesQuantity());
        $this->assertTrue($team->hasGame($game));
    }

    public function testItShouldNotAddDuplicateGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $team->addGame($game);
        $team->addGame($game);

        $this->assertEquals(1, $team->getGamesQuantity());
    }

    public function testItShouldRemoveGame(): void
    {
        $team = TeamMother::create();
        $game = GameMother::random();

        $team->addGame($game);
        $this->assertEquals(1, $team->getGamesQuantity());

        $team->removeGame($game);
        $this->assertEquals(0, $team->getGamesQuantity());
        $this->assertFalse($team->hasGame($game));
    }
}
