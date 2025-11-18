<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank;

use App\Contexts\Shared\Domain\Bus\Query\Query;

final readonly class VerifyPlayerRankQuery implements Query
{
    public function __construct(
        public string $username,
        public string $gameIdentifier
    ) {
    }
}
