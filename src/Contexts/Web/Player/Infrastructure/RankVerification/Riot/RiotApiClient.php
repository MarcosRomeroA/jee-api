<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\Riot;

use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Cliente para Riot Games API
 * Soporta: League of Legends
 *
 * Documentación: https://developer.riotgames.com/
 */
final class RiotApiClient
{
    // Platform routing values para LoL
    private const PLATFORM_ROUTES = [
        'br' => 'br1',
        'eune' => 'eun1',
        'euw' => 'euw1',
        'jp' => 'jp1',
        'kr' => 'kr',
        'lan' => 'la1',
        'las' => 'la2',
        'na' => 'na1',
        'oce' => 'oc1',
        'tr' => 'tr1',
        'ru' => 'ru',
        'ph' => 'ph2',
        'sg' => 'sg2',
        'th' => 'th2',
        'tw' => 'tw2',
        'vn' => 'vn2',
    ];

    // Regional routing values para Account API
    private const REGIONAL_ROUTES = [
        'br' => 'americas',
        'lan' => 'americas',
        'las' => 'americas',
        'na' => 'americas',
        'oce' => 'americas',
        'eune' => 'europe',
        'euw' => 'europe',
        'tr' => 'europe',
        'ru' => 'europe',
        'jp' => 'asia',
        'kr' => 'asia',
        'ph' => 'sea',
        'sg' => 'sea',
        'th' => 'sea',
        'tw' => 'sea',
        'vn' => 'sea',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
    ) {
    }

    /**
     * Obtiene cuenta por Riot ID (username#tag)
     */
    public function getAccountByRiotId(string $region, string $username, string $tag): array
    {
        $regionalRoute = self::REGIONAL_ROUTES[strtolower($region)] ?? 'americas';

        $url = sprintf(
            'https://%s.api.riotgames.com/riot/account/v1/accounts/by-riot-id/%s/%s',
            $regionalRoute,
            rawurlencode($username),
            rawurlencode($tag)
        );

        return $this->makeRequest($url);
    }

    /**
     * Obtiene entradas de liga (ranked) por PUUID
     */
    public function getLeagueEntriesByPuuid(string $region, string $puuid): array
    {
        $platformRoute = self::PLATFORM_ROUTES[strtolower($region)] ?? 'la2';

        $url = sprintf(
            'https://%s.api.riotgames.com/lol/league/v4/entries/by-puuid/%s',
            $platformRoute,
            $puuid
        );

        return $this->makeRequest($url);
    }

    /**
     * Obtiene rango de LoL dado region, username, tag
     */
    public function getLoLRank(string $region, string $username, string $tag): array
    {
        // 1. Obtener cuenta por Riot ID
        $account = $this->getAccountByRiotId($region, $username, $tag);

        if (empty($account) || !isset($account['puuid'])) {
            throw RankVerificationException::playerNotFound("$username#$tag");
        }

        // 2. Obtener entradas de liga directamente por PUUID
        $leagueEntries = $this->getLeagueEntriesByPuuid($region, $account['puuid']);

        return $this->extractRankFromLeagueEntries($leagueEntries);
    }

    /**
     * Extrae información de rango de las entradas de liga
     */
    public function extractRankFromLeagueEntries(array $leagueEntries): array
    {
        if (empty($leagueEntries)) {
            return [
                'rank' => 'Unranked',
                'tier' => null,
                'lp' => 0,
                'wins' => 0,
                'losses' => 0,
                'queueType' => null,
            ];
        }

        // Prioridad: RANKED_SOLO_5x5 > RANKED_FLEX_SR
        $soloQueue = null;
        $flexQueue = null;

        foreach ($leagueEntries as $entry) {
            if ($entry['queueType'] === 'RANKED_SOLO_5x5') {
                $soloQueue = $entry;
                break;
            }
            if ($entry['queueType'] === 'RANKED_FLEX_SR') {
                $flexQueue = $entry;
            }
        }

        $rankedEntry = $soloQueue ?? $flexQueue;

        if ($rankedEntry === null) {
            return [
                'rank' => 'Unranked',
                'tier' => null,
                'lp' => 0,
                'wins' => 0,
                'losses' => 0,
                'queueType' => null,
            ];
        }

        // Riot usa tier para el rango (GOLD, PLATINUM, etc.) y rank para la división (I, II, III, IV)
        return [
            'rank' => $rankedEntry['tier'] ?? 'Unranked',
            'tier' => $this->romanToNumber($rankedEntry['rank'] ?? null),
            'lp' => $rankedEntry['leaguePoints'] ?? 0,
            'wins' => $rankedEntry['wins'] ?? 0,
            'losses' => $rankedEntry['losses'] ?? 0,
            'queueType' => $rankedEntry['queueType'] ?? null,
        ];
    }

    /**
     * Convierte número romano a número
     */
    private function romanToNumber(?string $roman): ?string
    {
        if ($roman === null) {
            return null;
        }

        $map = [
            'I' => '1',
            'II' => '2',
            'III' => '3',
            'IV' => '4',
        ];

        return $map[$roman] ?? $roman;
    }

    private function makeRequest(string $url): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'X-Riot-Token' => $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(false);

            if ($statusCode === 404) {
                throw RankVerificationException::playerNotFound('Player not found on Riot API');
            }

            if ($statusCode === 401 || $statusCode === 403) {
                throw RankVerificationException::invalidApiKey();
            }

            if ($statusCode === 429) {
                throw RankVerificationException::rateLimitExceeded();
            }

            if ($statusCode !== 200) {
                $errorMessage = $content['status']['message'] ?? 'Unknown error';
                throw new RankVerificationException(
                    "Riot API error: $errorMessage",
                    'riot_api_error',
                    $statusCode
                );
            }

            return $content;
        } catch (RankVerificationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RankVerificationException::apiNotAvailable('Riot API: ' . $e->getMessage());
        }
    }
}
