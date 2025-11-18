<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank;

use App\Contexts\Web\Player\Domain\Service\RankVerifier;

final readonly class PlayerRankVerifier
{
    public function __construct(
        private RankVerifier $rankVerifier
    ) {
    }

    public function verify(string $username, string $gameIdentifier): array
    {
        return $this->rankVerifier->getRankInfo($username, $gameIdentifier);
    }
}
