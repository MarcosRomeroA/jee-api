<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Tournament\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Delete\TournamentDeleter;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Tests\Unit\Web\Tournament\Domain\TournamentMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class TournamentDeleterTest extends TestCase
{
    private TournamentRepository|MockObject $repository;
    private TournamentDeleter $deleter;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TournamentRepository::class);
        $this->deleter = new TournamentDeleter($this->repository);
    }

    public function testItShouldDeleteATournament(): void
    {
        $id = Uuid::random();
        $tournament = TournamentMother::create($id);

        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willReturn($tournament);

        $this->repository
            ->expects($this->once())
            ->method("save")
            ->with($tournament);

        $this->deleter->delete($id);

        $this->assertTrue($tournament->isDeleted());
    }

    public function testItShouldThrowExceptionWhenTournamentNotFound(): void
    {
        $id = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new TournamentNotFoundException($id->value()));

        $this->expectException(TournamentNotFoundException::class);

        $this->deleter->delete($id);
    }
}
