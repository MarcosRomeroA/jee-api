<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\TrackerGg;

use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Cliente para Tracker.gg API
 * Soporta: Counter-Strike 2
 *
 * Documentación: https://tracker.gg/developers/docs/getting-started
 */
final class TrackerGgApiClient
{
    private const BASE_URL = 'https://public-api.tracker.gg/v2';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
    ) {
    }

    /**
     * Obtiene estadísticas de CS2 por Steam ID
     */
    public function getCS2Stats(string $steamId): array
    {
        $url = sprintf(
            '%s/csgo/standard/profile/steam/%s',
            self::BASE_URL,
            $steamId
        );

        return $this->makeRequest($url);
    }

    /**
     * Extrae información de rango de CS2 del response
     */
    public function extractCS2Rank(array $data): array
    {
        $segments = $data['segments'] ?? [];

        foreach ($segments as $segment) {
            if (($segment['type'] ?? '') === 'overview') {
                $stats = $segment['stats'] ?? [];

                $rankName = $stats['rank']['metadata']['name'] ?? null;

                return [
                    'rank' => $rankName,
                    'tier' => null, // CS2 no tiene tiers dentro del rango
                    'wins' => $stats['wins']['value'] ?? 0,
                    'kills' => $stats['kills']['value'] ?? 0,
                    'deaths' => $stats['deaths']['value'] ?? 0,
                    'kd' => $stats['kd']['value'] ?? 0,
                    'headshotPct' => $stats['headshotPct']['value'] ?? 0,
                ];
            }
        }

        return [
            'rank' => null,
            'tier' => null,
            'wins' => 0,
            'kills' => 0,
            'deaths' => 0,
            'kd' => 0,
            'headshotPct' => 0,
        ];
    }

    private function makeRequest(string $url): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'TRN-Api-Key' => $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(false);

            if ($statusCode === 404) {
                throw RankVerificationException::playerNotFound('Player not found on Tracker.gg');
            }

            if ($statusCode === 401) {
                throw RankVerificationException::invalidApiKey();
            }

            if ($statusCode === 429) {
                throw RankVerificationException::rateLimitExceeded();
            }

            if ($statusCode !== 200) {
                $errorMessage = $content['errors'][0]['message'] ?? 'Unknown error';
                throw new RankVerificationException(
                    "Tracker.gg API error: $errorMessage",
                    'tracker_gg_api_error',
                    $statusCode
                );
            }

            return $content['data'] ?? [];
        } catch (RankVerificationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw RankVerificationException::apiNotAvailable('Tracker.gg: ' . $e->getMessage());
        }
    }
}
