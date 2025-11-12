<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface RankVerifier
{
    public function verifyRank(Player $player, Uuid $gameId): bool;
}

