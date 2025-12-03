<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\Henrik;

use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Cliente para Henrik's Valorant API
 * API gratuita para obtener datos de Valorant
 *
 * Documentación: https://docs.henrikdev.xyz/valorant.html
 */
final class HenrikApiClient
{
    private const BASE_URL = 'https://api.henrikdev.xyz/valorant';

    // Mapeo de regiones internas a regiones de la API
    private const REGION_MAP = [
        'las' => 'latam',
        'lan' => 'latam',
        'na' => 'na',
        'eu' => 'eu',
        'kr' => 'kr',
        'ap' => 'ap',
        'br' => 'br',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ?string $apiKey = null,
    ) {
    }

    /**
     * Obtiene información de la cuenta por nombre y tag
     */
    public function getAccount(string $username, string $tag): array
    {
        $url = sprintf(
            '%s/v1/account/%s/%s',
            self::BASE_URL,
            rawurlencode($username),
            rawurlencode($tag)
        );

        return $this->makeRequest($url);
    }

    /**
     * Obtiene el MMR (rank) actual del jugador
     */
    public function getMMR(string $region, string $username, string $tag): array
    {
        $apiRegion = self::REGION_MAP[strtolower($region)] ?? $region;

        $url = sprintf(
            '%s/v2/mmr/%s/%s/%s',
            self::BASE_URL,
            $apiRegion,
            rawurlencode($username),
            rawurlencode($tag)
        );

        return $this->makeRequest($url);
    }

    /**
     * Obtiene el historial de MMR del jugador
     */
    public function getMMRHistory(string $region, string $username, string $tag): array
    {
        $apiRegion = self::REGION_MAP[strtolower($region)] ?? $region;

        $url = sprintf(
            '%s/v1/mmr-history/%s/%s/%s',
            self::BASE_URL,
            $apiRegion,
            rawurlencode($username),
            rawurlencode($tag)
        );

        return $this->makeRequest($url);
    }

    /**
     * Extrae información de rango de la respuesta de MMR
     */
    public function extractRankInfo(array $data): array
    {
        $currentData = $data['current_data'] ?? $data['data']['current_data'] ?? [];

        $currentTier = $currentData['currenttier'] ?? null;
        $currentTierPatched = $currentData['currenttierpatched'] ?? null;
        $rankingInTier = $currentData['ranking_in_tier'] ?? 0;
        $elo = $currentData['elo'] ?? 0;

        // Separar el rango del tier (ej: "Gold 3" -> rank: "Gold", tier: "3")
        $rank = null;
        $tier = null;

        if ($currentTierPatched !== null) {
            // El formato es "Rank Tier" (ej: "Gold 3", "Diamond 1", "Immortal 2")
            if (preg_match('/^(.+?)\s+(\d+)$/', $currentTierPatched, $matches)) {
                $rank = $matches[1];
                $tier = $matches[2];
            } else {
                // Para rangos sin tier numérico (Radiant, Unranked)
                $rank = $currentTierPatched;
                $tier = null;
            }
        }

        return [
            'rank' => $rank,
            'tier' => $tier,
            'currentTierPatched' => $currentTierPatched,
            'rr' => $rankingInTier,
            'elo' => $elo,
        ];
    }

    private function makeRequest(string $url): array
    {
        try {
            $headers = [
                'Accept' => 'application/json',
            ];

            // Si hay API key, agregarla
            if ($this->apiKey !== null && $this->apiKey !== '') {
                $headers['Authorization'] = $this->apiKey;
            }

            $response = $this->httpClient->request('GET', $url, [
                'headers' => $headers,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(false);

            if ($statusCode === 404) {
                throw RankVerificationException::playerNotFound('Player not found on Henrik API');
            }

            if ($statusCode === 429) {
                throw RankVerificationException::rateLimitExceeded();
            }

            if ($statusCode !== 200) {
                $errorMessage = $content['errors'][0]['message'] ?? $content['message'] ?? 'Unknown error';
                throw new RankVerificationException(
                    "Henrik API error: $errorMessage",
                    'henrik_api_error',
                    $statusCode
                );
            }

            // Henrik API devuelve status en el body
            if (isset($content['status']) && $content['status'] !== 200) {
                $errorMessage = $content['message'] ?? 'Unknown error';
                throw RankVerificationException::playerNotFound($errorMessage);
            }

            return $content['data'] ?? $content;
        } catch (RankVerificationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RankVerificationException::apiNotAvailable('Henrik API: ' . $e->getMessage());
        }
    }
}
