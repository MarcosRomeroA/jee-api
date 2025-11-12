<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\Steam;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\Service\RankVerifier;

/**
 * Implementación de RankVerifier para Steam API
 * Soporta: Counter-Strike 2, Dota 2
 */
final class SteamRankVerifier implements RankVerifier
{
    public function __construct(
        private readonly SteamApiClient $steamApiClient
    ) {
    }

    public function verify(Player $player, Uuid $gameId): bool
    {
        try {
            // Obtener identificador del juego
            // Por ahora asumimos que es CS2 o Dota 2
            $gameIdentifier = 'cs2'; // Esto debería venir de la entidad Game

            $rankInfo = $this->getRankInfo($player->username(), $gameIdentifier);

            return !empty($rankInfo);
        } catch (RankVerificationException $e) {
            return false;
        }
    }

    public function getRankInfo(string $username, string $gameIdentifier): array
    {
        // 1. Resolver username a Steam ID
        $steamId = $this->steamApiClient->resolveVanityUrl($username);

        if ($steamId === null) {
            throw RankVerificationException::playerNotFound($username);
        }

        // 2. Verificar que el perfil sea público
        if (!$this->steamApiClient->isProfilePublic($steamId)) {
            throw new RankVerificationException("Steam profile is private");
        }

        // 3. Obtener información según el juego
        return match ($gameIdentifier) {
            'cs2' => $this->getCS2Rank($steamId),
            'dota2' => $this->getDota2Rank($steamId),
            default => throw new RankVerificationException("Game $gameIdentifier not supported by Steam API")
        };
    }

    /**
     * Obtiene información de Counter-Strike 2
     * Nota: CS2 no tiene una API oficial de ranking, se usan estadísticas
     */
    private function getCS2Rank(string $steamId): array
    {
        $stats = $this->steamApiClient->getCS2Stats($steamId);

        if (empty($stats)) {
            return [
                'rank' => 'UNKNOWN',
                'tier' => 'No stats available or profile private',
                'hours_played' => 0,
            ];
        }

        // Extraer estadísticas relevantes
        $kills = 0;
        $deaths = 0;
        $wins = 0;
        $roundsPlayed = 0;

        if (isset($stats['stats'])) {
            foreach ($stats['stats'] as $stat) {
                switch ($stat['name']) {
                    case 'total_kills':
                        $kills = $stat['value'];
                        break;
                    case 'total_deaths':
                        $deaths = $stat['value'];
                        break;
                    case 'total_wins':
                        $wins = $stat['value'];
                        break;
                    case 'total_rounds_played':
                        $roundsPlayed = $stat['value'];
                        break;
                }
            }
        }

        $kd = $deaths > 0 ? round($kills / $deaths, 2) : 0;

        return [
            'rank' => 'N/A', // CS2 ranking requires third-party APIs
            'tier' => 'Stats available',
            'kills' => $kills,
            'deaths' => $deaths,
            'wins' => $wins,
            'kd_ratio' => $kd,
            'rounds_played' => $roundsPlayed,
        ];
    }

    /**
     * Obtiene información de Dota 2
     */
    private function getDota2Rank(string $steamId): array
    {
        // Obtener historial de partidas
        $matches = $this->steamApiClient->getDota2MatchHistory($steamId, 20);

        if (empty($matches)) {
            return [
                'rank' => 'UNKNOWN',
                'tier' => 'No matches found or profile private',
                'matches_played' => 0,
            ];
        }

        // Calcular estadísticas básicas
        $totalMatches = count($matches);
        $wins = 0;

        foreach ($matches as $match) {
            // En Dota 2, determinar si ganó requiere más análisis
            // Por simplicidad, contamos partidas jugadas
        }

        return [
            'rank' => 'N/A', // Dota 2 ranking requires OpenDota API o similar
            'tier' => 'Match history available',
            'matches_played' => $totalMatches,
            'recent_matches' => $totalMatches,
        ];
    }
}

