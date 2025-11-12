<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Team;
use PHPUnit\Framework\TestCase;

final class TeamTest extends TestCase
{
    public function testItShouldCreateATeam(): void
    {
        $id = Uuid::random();
        $name = 'Los Campeones';
        $image = 'https://example.com/team.jpg';

        $team = new Team(
            $id,
            GameMother::random(),
            UserMother::random(),
            $name,
            $image
        );

        $this->assertEquals($id, $team->id());
        $this->assertEquals($name, $team->name());
        $this->assertEquals($image, $team->image());
        $this->assertEquals(0, $team->playersQuantity());
    }

    public function testItShouldUpdateTeam(): void
    {
        $team = TeamMother::create();
        $newName = 'Updated Team Name';
        $newImage = 'https://example.com/new-image.jpg';

        $team->update($newName, $newImage);

        $this->assertEquals($newName, $team->name());
        $this->assertEquals($newImage, $team->image());
    }

    public function testItShouldVerifyOwner(): void
    {
        $ownerId = Uuid::random();
        $team = TeamMother::withOwner($ownerId);

        $this->assertTrue($team->isOwner($ownerId));
        $this->assertFalse($team->isOwner(Uuid::random()));
    }

    public function testItShouldReturnPlayersQuantity(): void
    {
        $team = TeamMother::create();

        $this->assertEquals(0, $team->playersQuantity());
    }
}

