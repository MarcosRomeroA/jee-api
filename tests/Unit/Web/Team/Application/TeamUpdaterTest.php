<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Update\TeamUpdater;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Tests\Unit\Web\Team\Domain\TeamMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class TeamUpdaterTest extends TestCase
{
    private TeamRepository|MockObject $repository;
    private TeamUpdater $updater;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TeamRepository::class);
        $this->updater = new TeamUpdater($this->repository);
    }

    public function testItShouldUpdateATeam(): void
    {
        $id = Uuid::random();
        $newName = 'Updated Team Name';
        $newImage = 'https://example.com/new-image.jpg';

        $team = TeamMother::create($id);

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($team);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($team);

        $this->updater->update($id, $newName, $newImage);

        $this->assertEquals($newName, $team->name());
        $this->assertEquals($newImage, $team->image());
    }

    public function testItShouldThrowExceptionWhenTeamNotFound(): void
    {
        $id = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->expectException(TeamNotFoundException::class);

        $this->updater->update($id, 'New Name', null);
    }
}

