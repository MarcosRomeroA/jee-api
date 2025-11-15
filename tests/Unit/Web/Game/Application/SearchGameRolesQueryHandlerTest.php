<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Game\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Application\Search\SearchGameRolesQuery;
use App\Contexts\Web\Game\Application\Search\SearchGameRolesQueryHandler;
use App\Contexts\Web\Game\Domain\GameRoleRepository;
use App\Tests\Unit\Web\Game\Domain\GameRoleMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class SearchGameRolesQueryHandlerTest extends TestCase
{
    private GameRoleRepository|MockObject $repository;
    private SearchGameRolesQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GameRoleRepository::class);
        $this->handler = new SearchGameRolesQueryHandler($this->repository);
    }

    public function testItShouldSearchGameRoles(): void
    {
        $gameId = Uuid::random();
        $gameRole1 = GameRoleMother::create();
        $gameRole2 = GameRoleMother::create();
        $gameRole3 = GameRoleMother::create();

        $expectedRoles = [$gameRole1, $gameRole2, $gameRole3];

        $this->repository
            ->expects($this->once())
            ->method("findByGame")
            ->with($gameId)
            ->willReturn($expectedRoles);

        $query = new SearchGameRolesQuery($gameId->value());
        $result = ($this->handler)($query);

        $this->assertInstanceOf(
            \App\Contexts\Web\Game\Application\Shared\GameRoleCollectionResponse::class,
            $result,
        );
        $this->assertCount(3, $result->gameRoles);
        $this->assertIsArray($result->toArray());
        $this->assertCount(3, $result->toArray());
    }

    public function testItShouldReturnEmptyArrayWhenNoRolesFound(): void
    {
        $gameId = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method("findByGame")
            ->with($gameId)
            ->willReturn([]);

        $query = new SearchGameRolesQuery($gameId->value());
        $result = ($this->handler)($query);

        $this->assertInstanceOf(
            \App\Contexts\Web\Game\Application\Shared\GameRoleCollectionResponse::class,
            $result,
        );
        $this->assertCount(0, $result->gameRoles);
        $this->assertIsArray($result->toArray());
        $this->assertCount(0, $result->toArray());
    }
}
