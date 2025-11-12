<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Delete\TeamDeleter;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Tests\Unit\Web\Team\Domain\TeamMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class TeamDeleterTest extends TestCase
{
    private TeamRepository|MockObject $repository;
    private TeamDeleter $deleter;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TeamRepository::class);
        $this->deleter = new TeamDeleter($this->repository);
    }

    public function testItShouldDeleteATeam(): void
    {
        $id = Uuid::random();
        $team = TeamMother::create($id);

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($team);

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with($team);

        $this->deleter->delete($id);
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

        $this->deleter->delete($id);
    }
}

