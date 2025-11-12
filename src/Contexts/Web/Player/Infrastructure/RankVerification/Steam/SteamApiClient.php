<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\Steam;

use App\Contexts\Shared\Domain\Http\HttpClient;
use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;

/**
 * Cliente para Steam Web API
 * Soporta: Counter-Strike 2, Dota 2
 *
 * Documentación: https://developer.valvesoftware.com/wiki/Steam_Web_API
 */
final class SteamApiClient
{
    private const STEAM_API_BASE_URL = 'https://api.steampowered.com';

    // App IDs de los juegos
    private const APP_IDS = [
        'cs2' => 730,      // Counter-Strike 2 (antes CS:GO)
        'dota2' => 570,    // Dota 2
    ];

    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly string $apiKey
    ) {
    }

    /**
     * Resuelve el Vanity URL a Steam ID
     * Convierte un nombre de usuario a Steam ID64
     */
    public function resolveVanityUrl(string $vanityUrl): ?string
    {
        $url = self::STEAM_API_BASE_URL . '/ISteamUser/ResolveVanityURL/v1/';

        $response = $this->httpClient->get($url, [], [
            'key' => $this->apiKey,
            'vanityurl' => $vanityUrl
        ]);

        if (!$response->isSuccessful()) {
            throw RankVerificationException::apiNotAvailable('Steam API');
        }

        $body = $response->body();

        if (isset($body['response']['success']) && $body['response']['success'] === 1) {
            return $body['response']['steamid'] ?? null;
        }

        return null;
    }

    /**
     * Obtiene resumen del jugador
     */
    public function getPlayerSummaries(string $steamId): array
    {
        $url = self::STEAM_API_BASE_URL . '/ISteamUser/GetPlayerSummaries/v2/';

        $response = $this->httpClient->get($url, [], [
            'key' => $this->apiKey,
            'steamids' => $steamId
        ]);

        if (!$response->isSuccessful()) {
            throw RankVerificationException::apiNotAvailable('Steam API - Player Summaries');
        }

        $body = $response->body();

        return $body['response']['players'][0] ?? [];
    }

    /**
     * Obtiene estadísticas de usuario para Counter-Strike 2
     */
    public function getCS2Stats(string $steamId): array
    {
        $url = self::STEAM_API_BASE_URL . '/ISteamUserStats/GetUserStatsForGame/v2/';

        $response = $this->httpClient->get($url, [], [
            'key' => $this->apiKey,
            'steamid' => $steamId,
            'appid' => self::APP_IDS['cs2']
        ]);

        if (!$response->isSuccessful()) {
            // Puede fallar si el perfil es privado o no tiene stats
            return [];
        }

        return $response->body()['playerstats'] ?? [];
    }

    /**
     * Obtiene historial de partidas de Dota 2
     */
    public function getDota2MatchHistory(string $steamId, int $matchesRequested = 10): array
    {
        // Convertir Steam ID64 a Account ID (últimos 32 bits)
        $accountId = $this->steamId64ToAccountId($steamId);

        $url = self::STEAM_API_BASE_URL . '/IDOTA2Match_570/GetMatchHistory/v1/';

        $response = $this->httpClient->get($url, [], [
            'key' => $this->apiKey,
            'account_id' => $accountId,
            'matches_requested' => $matchesRequested
        ]);

        if (!$response->isSuccessful()) {
            return [];
        }

        return $response->body()['result']['matches'] ?? [];
    }

    /**
     * Obtiene detalles de una partida de Dota 2
     */
    public function getDota2MatchDetails(string $matchId): array
    {
        $url = self::STEAM_API_BASE_URL . '/IDOTA2Match_570/GetMatchDetails/v1/';

        $response = $this->httpClient->get($url, [], [
            'key' => $this->apiKey,
            'match_id' => $matchId
        ]);

        if (!$response->isSuccessful()) {
            return [];
        }

        return $response->body()['result'] ?? [];
    }

    /**
     * Convierte Steam ID64 a Account ID
     */
    private function steamId64ToAccountId(string $steamId64): int
    {
        // Account ID = Steam ID64 - 76561197960265728
        return (int)$steamId64 - 76561197960265728;
    }

    /**
     * Verifica si un perfil es público
     */
    public function isProfilePublic(string $steamId): bool
    {
        $summary = $this->getPlayerSummaries($steamId);

        // communityvisibilitystate: 3 = público
        return isset($summary['communityvisibilitystate']) && $summary['communityvisibilitystate'] === 3;
    }
}

