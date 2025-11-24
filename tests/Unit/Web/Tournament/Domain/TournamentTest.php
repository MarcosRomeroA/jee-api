<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Tournament;
use PHPUnit\Framework\TestCase;

final class TournamentTest extends TestCase
{
    public function testItShouldCreateATournament(): void
    {
        $id = Uuid::random();
        $name = 'Championship 2025';
        $maxTeams = 32;
        $startAt = new \DateTimeImmutable('+1 day');
        $endAt = new \DateTimeImmutable('+7 days');

        $tournament = new Tournament(
            $id,
            GameMother::random(),
            TournamentStatusMother::active(),
            UserMother::random(),
            $name,
            'Championship description',
            'Championship rules',
            $maxTeams,
            true,
            'https://example.com/tournament.jpg',
            '$5000 USD',
            'NA',
            $startAt,
            $endAt
        );

        $this->assertEquals($id, $tournament->getId());
        $this->assertEquals($name, $tournament->getName());
        $this->assertEquals($maxTeams, $tournament->getMaxTeams());
        $this->assertEquals(0, $tournament->getRegisteredTeams());
        $this->assertTrue($tournament->getIsOfficial());
        $this->assertFalse($tournament->isDeleted());
    }

    public function testItShouldUpdateTournament(): void
    {
        $tournament = TournamentMother::create();
        $newName = 'Updated Tournament Name';
        $newMaxTeams = 64;
        $newStartAt = new \DateTimeImmutable('+2 days');
        $newEndAt = new \DateTimeImmutable('+10 days');

        $tournament->update(
            $newName,
            'Updated description',
            'Updated rules',
            $newMaxTeams,
            true,
            'https://example.com/new-image.jpg',
            '$10000 USD',
            'EU',
            $newStartAt,
            $newEndAt
        );

        $this->assertEquals($newName, $tournament->getName());
        $this->assertEquals($newMaxTeams, $tournament->getMaxTeams());
        $this->assertTrue($tournament->getIsOfficial());
        $this->assertNotNull($tournament->getUpdatedAt());
    }

    public function testItShouldIncrementRegisteredTeams(): void
    {
        $tournament = TournamentMother::create();
        $initialTeams = $tournament->getRegisteredTeams();

        $tournament->incrementRegisteredTeams();

        $this->assertEquals($initialTeams + 1, $tournament->getRegisteredTeams());
    }

    public function testItShouldDecrementRegisteredTeams(): void
    {
        $tournament = TournamentMother::create();
        $tournament->incrementRegisteredTeams();
        $tournament->incrementRegisteredTeams();

        $teamsAfterIncrement = $tournament->getRegisteredTeams();
        $tournament->decrementRegisteredTeams();

        $this->assertEquals($teamsAfterIncrement - 1, $tournament->getRegisteredTeams());
    }

    public function testItShouldNotDecrementBelowZero(): void
    {
        $tournament = TournamentMother::create();

        $tournament->decrementRegisteredTeams();

        $this->assertEquals(0, $tournament->getRegisteredTeams());
    }

    public function testItShouldAssignResponsible(): void
    {
        $tournament = TournamentMother::create();
        $newResponsible = UserMother::random();

        $tournament->assignResponsible($newResponsible);

        $this->assertEquals($newResponsible, $tournament->getResponsible());
        $this->assertNotNull($tournament->getUpdatedAt());
    }

    public function testItShouldMarkAsDeleted(): void
    {
        $tournament = TournamentMother::create();

        $tournament->delete();

        $this->assertTrue($tournament->isDeleted());
        $this->assertNotNull($tournament->getDeletedAt());
    }
}
