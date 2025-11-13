<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Find\TeamFinder;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Tests\Unit\Web\Team\Domain\TeamMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class TeamFinderTest extends TestCase
{
    private TeamRepository|MockObject $repository;
    private TeamFinder $finder;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TeamRepository::class);
        $this->finder = new TeamFinder($this->repository);
    }

    public function testItShouldFindATeam(): void
    {
        $id = Uuid::random();
        $team = TeamMother::create($id, 'Test Team');

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($team);

        $result = $this->finder->find($id);

        $this->assertEquals($team, $result);
        $this->assertEquals('Test Team', $result->name());
    }

    public function testItShouldThrowExceptionWhenTeamNotFound(): void
    {
        $id = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willThrowException(new TeamNotFoundException($id->value()));

        $this->expectException(TeamNotFoundException::class);

        $this->finder->find($id);
    }
}

