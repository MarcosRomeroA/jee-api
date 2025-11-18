<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank;

use App\Contexts\Shared\Domain\Bus\Query\QueryHandler;

final readonly class VerifyPlayerRankQueryHandler implements QueryHandler
{
    public function __construct(
        private PlayerRankVerifier $playerRankVerifier
    ) {
    }

    public function __invoke(VerifyPlayerRankQuery $query): array
    {
        return $this->playerRankVerifier->verify($query->username, $query->gameIdentifier);
    }
}
