<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\Service\RankVerifier;
use App\Contexts\Web\Player\Infrastructure\RankVerification\Riot\RiotRankVerifier;
use App\Contexts\Web\Player\Infrastructure\RankVerification\Steam\SteamRankVerifier;

/**
 * Implementación composite que delega a diferentes APIs según el juego
 * Determina automáticamente qué API usar basándose en el juego
 */
final class CompositeRankVerifier implements RankVerifier
{
    // Mapeo de juegos a proveedores de API
    private const GAME_PROVIDERS = [
        'league-of-legends' => 'riot',
        'valorant' => 'riot',
        'tft' => 'riot',
        'cs2' => 'steam',
        'counter-strike-2' => 'steam',
        'dota2' => 'steam',
        'dota-2' => 'steam',
    ];

    public function __construct(
        private readonly RiotRankVerifier $riotRankVerifier,
        private readonly SteamRankVerifier $steamRankVerifier
    ) {
    }

    public function verify(Player $player, Uuid $gameId): bool
    {
        $verifier = $this->getVerifierForGame($gameId);

        return $verifier->verify($player, $gameId);
    }

    public function getRankInfo(string $username, string $gameIdentifier): array
    {
        $provider = self::GAME_PROVIDERS[$gameIdentifier] ?? null;

        if ($provider === null) {
            throw new RankVerificationException("Game $gameIdentifier is not supported");
        }

        $verifier = $this->getVerifierByProvider($provider);

        return $verifier->getRankInfo($username, $gameIdentifier);
    }

    /**
     * Obtiene el verificador apropiado según el juego
     */
    private function getVerifierForGame(Uuid $gameId): RankVerifier
    {
        // TODO: Aquí deberíamos consultar la entidad Game para obtener el identificador
        // Por ahora, asumimos Riot como default
        return $this->riotRankVerifier;
    }

    /**
     * Obtiene el verificador según el proveedor
     */
    private function getVerifierByProvider(string $provider): RankVerifier
    {
        return match ($provider) {
            'riot' => $this->riotRankVerifier,
            'steam' => $this->steamRankVerifier,
            default => throw new RankVerificationException("Provider $provider not supported")
        };
    }
}

