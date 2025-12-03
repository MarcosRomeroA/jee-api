<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\OpenDota;

use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Cliente para OpenDota API
 * API gratuita para obtener datos de Dota 2
 *
 * Documentación: https://docs.opendota.com/
 */
final class OpenDotaApiClient
{
    private const BASE_URL = 'https://api.opendota.com/api';

    // Mapeo de rank_tier a nombres de rango
    // rank_tier es un número de 2 dígitos: primer dígito = medal, segundo = stars
    // Medals: 1=Herald, 2=Guardian, 3=Crusader, 4=Archon, 5=Legend, 6=Ancient, 7=Divine, 8=Immortal
    private const RANK_NAMES = [
        1 => 'Herald',
        2 => 'Guardian',
        3 => 'Crusader',
        4 => 'Archon',
        5 => 'Legend',
        6 => 'Ancient',
        7 => 'Divine',
        8 => 'Immortal',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ?string $apiKey = null,
    ) {
    }

    /**
     * Obtiene información del jugador por Steam ID (32-bit account ID)
     */
    public function getPlayer(string $accountId): array
    {
        $url = sprintf('%s/players/%s', self::BASE_URL, $accountId);

        return $this->makeRequest($url);
    }

    /**
     * Obtiene estadísticas de victorias/derrotas del jugador
     */
    public function getPlayerWinLoss(string $accountId): array
    {
        $url = sprintf('%s/players/%s/wl', self::BASE_URL, $accountId);

        return $this->makeRequest($url);
    }

    /**
     * Obtiene partidas recientes del jugador
     */
    public function getRecentMatches(string $accountId, int $limit = 20): array
    {
        $url = sprintf('%s/players/%s/recentMatches', self::BASE_URL, $accountId);

        return $this->makeRequest($url);
    }

    /**
     * Convierte Steam ID64 a Account ID (32-bit)
     */
    public function steamId64ToAccountId(string $steamId64): string
    {
        // Account ID = Steam ID64 - 76561197960265728
        $accountId = (int) $steamId64 - 76561197960265728;
        return (string) $accountId;
    }

    /**
     * Extrae información de rango del response del player
     */
    public function extractRankInfo(array $playerData): array
    {
        $rankTier = $playerData['rank_tier'] ?? null;
        $leaderboardRank = $playerData['leaderboard_rank'] ?? null;

        if ($rankTier === null) {
            return [
                'rank' => 'Unranked',
                'tier' => null,
                'leaderboardRank' => null,
            ];
        }

        // rank_tier es un número de 2 dígitos
        // Primer dígito = medal (1-8)
        // Segundo dígito = stars (1-5, 0 para Immortal)
        $medal = (int) floor($rankTier / 10);
        $stars = $rankTier % 10;

        $rankName = self::RANK_NAMES[$medal] ?? 'Unknown';

        // Immortal no tiene stars, usa leaderboard rank
        if ($medal === 8) {
            return [
                'rank' => 'Immortal',
                'tier' => null,
                'leaderboardRank' => $leaderboardRank,
            ];
        }

        return [
            'rank' => $rankName,
            'tier' => $stars > 0 ? (string) $stars : null,
            'leaderboardRank' => null,
        ];
    }

    private function makeRequest(string $url): array
    {
        try {
            $queryParams = [];

            // Si hay API key, agregarla como query param
            if ($this->apiKey !== null && $this->apiKey !== '') {
                $queryParams['api_key'] = $this->apiKey;
            }

            $response = $this->httpClient->request('GET', $url, [
                'query' => $queryParams,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(false);

            if ($statusCode === 404) {
                throw RankVerificationException::playerNotFound('Player not found on OpenDota');
            }

            if ($statusCode === 429) {
                throw RankVerificationException::rateLimitExceeded();
            }

            if ($statusCode !== 200) {
                $errorMessage = $content['error'] ?? 'Unknown error';
                throw new RankVerificationException(
                    "OpenDota API error: $errorMessage",
                    'opendota_api_error',
                    $statusCode
                );
            }

            return $content;
        } catch (RankVerificationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RankVerificationException::apiNotAvailable('OpenDota API: ' . $e->getMessage());
        }
    }
}
