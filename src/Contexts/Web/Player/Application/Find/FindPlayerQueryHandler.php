<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Application\Shared\PlayerResponse;

final class FindPlayerQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly PlayerFinder $finder
    ) {
    }

    public function __invoke(FindPlayerQuery $query): PlayerResponse
    {
        $player = $this->finder->find(new Uuid($query->id));
        return PlayerResponse::fromPlayer($player);
    }
}

