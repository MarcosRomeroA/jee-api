<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Infrastructure;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\RankVerifier;

/**
 * Stub para tests unitarios - No hace verificación real
 */
final class RankVerifierServiceStub implements RankVerifier
{
    public function verifyRank(Player $player, Uuid $gameId): bool
    {
        // No hace nada en tests, siempre retorna false
        return false;
    }
}

