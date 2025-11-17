<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\FindAllByGame;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Application\Search\GameRolesSearcher;
use App\Contexts\Web\Game\Application\Shared\GameRoleCollectionResponse;

final readonly class FindAllGameRolesByGameQueryHandler implements QueryHandler
{
    public function __construct(
        private GameRolesSearcher $searcher
    ) {
    }

    public function __invoke(FindAllGameRolesByGameQuery $query): GameRoleCollectionResponse
    {
        $gameId = new Uuid($query->gameId);

        return ($this->searcher)($gameId);
    }
}
