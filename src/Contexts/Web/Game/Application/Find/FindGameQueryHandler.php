<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Application\Shared\GameResponse;

final class FindGameQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly GameFinder $finder
    ) {
    }

    public function __invoke(FindGameQuery $query): GameResponse
    {
        $game = $this->finder->find(new Uuid($query->id));
        return GameResponse::fromGame($game);
    }
}

