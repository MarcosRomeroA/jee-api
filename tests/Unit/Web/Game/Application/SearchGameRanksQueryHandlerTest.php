<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Game\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Application\Search\GameRanksSearcher;
use App\Contexts\Web\Game\Application\Search\SearchGameRanksQuery;
use App\Contexts\Web\Game\Application\Search\SearchGameRanksQueryHandler;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Tests\Unit\Web\Game\Domain\GameRankMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class SearchGameRanksQueryHandlerTest extends TestCase
{
    private GameRankRepository|MockObject $repository;
    private SearchGameRanksQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GameRankRepository::class);
        $searcher = new GameRanksSearcher($this->repository);
        $this->handler = new SearchGameRanksQueryHandler($searcher);
    }

    public function testItShouldSearchGameRanks(): void
    {
        $gameId = Uuid::random();
        $gameRank1 = GameRankMother::create(level: 1);
        $gameRank2 = GameRankMother::create(level: 2);
        $gameRank3 = GameRankMother::create(level: 3);

        $expectedRanks = [$gameRank1, $gameRank2, $gameRank3];

        $this->repository
            ->expects($this->once())
            ->method("findByGame")
            ->with($gameId)
            ->willReturn($expectedRanks);

        $query = new SearchGameRanksQuery($gameId->value());
        $result = ($this->handler)($query);

        $this->assertInstanceOf(
            \App\Contexts\Web\Game\Application\Shared\GameRankCollectionResponse::class,
            $result,
        );
        $this->assertCount(3, $result->gameRanks);
        $this->assertIsArray($result->toArray());
        $this->assertCount(3, $result->toArray());
    }

    public function testItShouldReturnEmptyArrayWhenNoRanksFound(): void
    {
        $gameId = Uuid::random();

        $this->repository
            ->expects($this->once())
            ->method("findByGame")
            ->with($gameId)
            ->willReturn([]);

        $query = new SearchGameRanksQuery($gameId->value());
        $result = ($this->handler)($query);

        $this->assertInstanceOf(
            \App\Contexts\Web\Game\Application\Shared\GameRankCollectionResponse::class,
            $result,
        );
        $this->assertCount(0, $result->gameRanks);
        $this->assertIsArray($result->toArray());
        $this->assertCount(0, $result->toArray());
    }
}
