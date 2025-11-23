<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Team\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Create\TeamCreator;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Team\Domain\UserMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class TeamCreatorTest extends TestCase
{
    private TeamRepository|MockObject $teamRepository;
    private UserRepository|MockObject $userRepository;
    private TeamCreator $creator;

    protected function setUp(): void
    {
        $this->teamRepository = $this->createMock(TeamRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->creator = new TeamCreator(
            $this->teamRepository,
            $this->userRepository,
        );
    }

    public function testItShouldCreateATeam(): void
    {
        $id = Uuid::random();
        $creatorId = Uuid::random();
        $name = "Los Campeones";
        $description = "A professional gaming team";
        $image = "https://example.com/team.jpg";

        $creator = UserMother::create($creatorId);

        $this->teamRepository
            ->expects($this->once())
            ->method("existsById")
            ->with($id)
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($creatorId)
            ->willReturn($creator);

        $this->teamRepository
            ->expects($this->once())
            ->method("save")
            ->with(
                $this->callback(function (Team $team) use (
                    $id,
                    $name,
                    $description,
                ) {
                    return $team->id()->equals($id) &&
                        $team->name() === $name &&
                        $team->description() === $description;
                }),
            );

        $this->creator->createOrUpdate($id, $name, $description, $image, $creatorId);
    }

    public function testItShouldThrowExceptionWhenUserNotFound(): void
    {
        $id = Uuid::random();
        $creatorId = Uuid::random();

        $this->teamRepository
            ->expects($this->once())
            ->method("existsById")
            ->with($id)
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method("findById")
            ->with($creatorId)
            ->willThrowException(
                new UserNotFoundException($creatorId->value()),
            );

        $this->expectException(UserNotFoundException::class);

        $this->creator->createOrUpdate(
            $id,
            "Team Name",
            "Team description",
            null,
            $creatorId,
        );
    }
}
