<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerifier;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\RankVerifier;

/**
 * Service to verify player ranks with external APIs
 * (Riot API for LoL/Valorant, Steam API for Counter/Dota, etc.)
 */
final class RankVerifierService implements RankVerifier
{
    public function __construct(
        // Inject external API clients here
    ) {
    }

    public function verifyRank(Player $player, Uuid $gameId): bool
    {
        // TODO: Implement verification logic based on game
        // - For Riot games (LoL, Valorant): Use Riot API
        // - For Steam games (Counter, Dota): Use Steam API

        return false;
    }
}

