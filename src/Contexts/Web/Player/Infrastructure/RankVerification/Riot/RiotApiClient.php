<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\Riot;

use App\Contexts\Shared\Domain\Http\HttpClient;
use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;

/**
 * Cliente para Riot Games API
 * Soporta: League of Legends, Valorant, TFT
 *
 * Documentación: https://developer.riotgames.com/
 */
final class RiotApiClient
{
    private const RIOT_API_BASE_URL = 'https://%s.api.riotgames.com';

    // Regiones disponibles
    private const REGIONS = [
        'americas' => ['NA', 'BR', 'LAN', 'LAS'],
        'asia' => ['KR', 'JP'],
        'europe' => ['EUW', 'EUNE', 'TR', 'RU'],
        'sea' => ['OCE', 'PH', 'SG', 'TH', 'TW', 'VN'],
    ];

    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly string $apiKey,
        private readonly string $defaultRegion = 'americas'
    ) {
    }

    /**
     * Obtiene información de summoner por nombre (League of Legends)
     */
    public function getSummonerByName(string $summonerName, string $region = 'na1'): array
    {
        $url = sprintf(
            self::RIOT_API_BASE_URL . '/lol/summoner/v4/summoners/by-name/%s',
            $region,
            urlencode($summonerName)
        );

        $response = $this->httpClient->get($url, [
            'X-Riot-Token' => $this->apiKey
        ]);

        if (!$response->isSuccessful()) {
            $this->handleError($response->statusCode(), $summonerName);
        }

        return $response->body();
    }

    /**
     * Obtiene entradas de liga por summoner ID (League of Legends)
     */
    public function getLeagueEntriesBySummonerId(string $summonerId, string $region = 'na1'): array
    {
        $url = sprintf(
            self::RIOT_API_BASE_URL . '/lol/league/v4/entries/by-summoner/%s',
            $region,
            $summonerId
        );

        $response = $this->httpClient->get($url, [
            'X-Riot-Token' => $this->apiKey
        ]);

        if (!$response->isSuccessful()) {
            throw RankVerificationException::apiNotAvailable('Riot Games - League Entries');
        }

        return $response->body();
    }

    /**
     * Obtiene cuenta por Riot ID (nombre#tag) - Usado para Valorant y TFT
     */
    public function getAccountByRiotId(string $gameName, string $tagLine): array
    {
        $url = sprintf(
            self::RIOT_API_BASE_URL . '/riot/account/v1/accounts/by-riot-id/%s/%s',
            $this->defaultRegion,
            urlencode($gameName),
            urlencode($tagLine)
        );

        $response = $this->httpClient->get($url, [
            'X-Riot-Token' => $this->apiKey
        ]);

        if (!$response->isSuccessful()) {
            $this->handleError($response->statusCode(), $gameName);
        }

        return $response->body();
    }

    /**
     * Obtiene información de ranked de Valorant por PUUID
     */
    public function getValorantRankByPuuid(string $puuid, string $region = 'na'): array
    {
        // Valorant API está en beta, puede no estar disponible en todas las regiones
        $url = sprintf(
            self::RIOT_API_BASE_URL . '/val/ranked/v1/by-puuid/%s',
            $region,
            $puuid
        );

        $response = $this->httpClient->get($url, [
            'X-Riot-Token' => $this->apiKey
        ]);

        if (!$response->isSuccessful()) {
            throw RankVerificationException::apiNotAvailable('Riot Games - Valorant Ranked');
        }

        return $response->body();
    }

    /**
     * Maneja errores de la API
     */
    private function handleError(int $statusCode, string $username): void
    {
        match ($statusCode) {
            401, 403 => throw RankVerificationException::invalidApiKey(),
            404 => throw RankVerificationException::playerNotFound($username),
            429 => throw RankVerificationException::rateLimitExceeded(),
            default => throw RankVerificationException::apiNotAvailable('Riot Games API')
        };
    }
}

