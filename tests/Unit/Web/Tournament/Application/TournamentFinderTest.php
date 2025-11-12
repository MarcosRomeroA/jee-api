<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Tournament\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Find\TournamentFinder;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Tests\Unit\Web\Tournament\Domain\TournamentMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class TournamentFinderTest extends TestCase
{
    private TournamentRepository|MockObject $repository;
    private TournamentFinder $finder;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TournamentRepository::class);
        $this->finder = new TournamentFinder($this->repository);
    }

    public function testItShouldFindATournament(): void
    {
        $id = Uuid::random();
        $tournament = TournamentMother::create($id, 'Championship 2025');

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($tournament);

        $result = $this->finder->find($id);

        $this->assertEquals($tournament, $result);
        $this->assertEquals('Championship 2025', $result->name());
    }

    public function testItShouldThrowExceptionWhenTournamentNotFound(): void
    {
        $id = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->expectException(TournamentNotFoundException::class);

        $this->finder->find($id);
    }
}

