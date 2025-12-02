<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\TrackerGg;

use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TrackerGgApiClient
{
    private const BASE_URL = 'https://public-api.tracker.gg/v2';

    // Mapeo de game IDs internos a identificadores de Tracker.gg
    private const GAME_IDENTIFIERS = [
        '550e8400-e29b-41d4-a716-446655440080' => 'valorant',
        '550e8400-e29b-41d4-a716-446655440081' => 'lol',
        '550e8400-e29b-41d4-a716-446655440082' => 'csgo',
        '550e8400-e29b-41d4-a716-446655440083' => 'dota2',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
    ) {
    }

    public function getPlayerStats(string $gameId, string $accountId): array
    {
        $gameIdentifier = self::GAME_IDENTIFIERS[$gameId] ?? null;

        if ($gameIdentifier === null) {
            throw RankVerificationException::gameNotSupported($gameId);
        }

        return match ($gameIdentifier) {
            'valorant' => $this->getValorantStats($accountId),
            'csgo' => $this->getCs2Stats($accountId),
            default => throw RankVerificationException::gameNotSupported($gameIdentifier),
        };
    }

    private function getValorantStats(string $riotId): array
    {
        // Riot ID format: Username#Tag
        $parts = explode('#', $riotId);
        if (count($parts) !== 2) {
            throw RankVerificationException::invalidAccountFormat('Riot ID must be in format Username#Tag');
        }

        [$username, $tag] = $parts;
        $encodedUsername = rawurlencode($username);
        $encodedTag = rawurlencode($tag);

        $url = sprintf(
            '%s/valorant/standard/profile/riot/%s%%23%s',
            self::BASE_URL,
            $encodedUsername,
            $encodedTag
        );

        return $this->makeRequest($url);
    }

    private function getCs2Stats(string $steamId): array
    {
        $url = sprintf(
            '%s/csgo/standard/profile/steam/%s',
            self::BASE_URL,
            $steamId
        );

        return $this->makeRequest($url);
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

    public function extractRankFromResponse(string $gameId, array $data): ?array
    {
        $gameIdentifier = self::GAME_IDENTIFIERS[$gameId] ?? null;

        return match ($gameIdentifier) {
            'valorant' => $this->extractValorantRank($data),
            'csgo' => $this->extractCs2Rank($data),
            default => null,
        };
    }

    private function extractValorantRank(array $data): ?array
    {
        // Buscar en segments el tipo "season" que tiene el rango competitivo
        $segments = $data['segments'] ?? [];

        foreach ($segments as $segment) {
            if (($segment['type'] ?? '') === 'season') {
                $stats = $segment['stats'] ?? [];
                $rank = $stats['rank']['metadata']['tierName'] ?? null;

                if ($rank !== null) {
                    return [
                        'rank' => $rank,
                        'tier' => $stats['rank']['metadata']['divisionName'] ?? null,
                        'rr' => $stats['rank']['value'] ?? 0,
                        'peakRank' => $stats['peakRank']['metadata']['tierName'] ?? null,
                    ];
                }
            }
        }

        // Fallback: buscar en el overview
        $platformInfo = $data['platformInfo'] ?? [];
        $metadata = $data['metadata'] ?? [];

        return [
            'rank' => $metadata['currentRank'] ?? null,
            'tier' => null,
            'rr' => 0,
            'peakRank' => null,
        ];
    }

    private function extractCs2Rank(array $data): ?array
    {
        $segments = $data['segments'] ?? [];

        foreach ($segments as $segment) {
            if (($segment['type'] ?? '') === 'overview') {
                $stats = $segment['stats'] ?? [];

                return [
                    'rank' => $stats['rank']['metadata']['name'] ?? null,
                    'tier' => null,
                    'wins' => $stats['wins']['value'] ?? 0,
                    'kills' => $stats['kills']['value'] ?? 0,
                    'deaths' => $stats['deaths']['value'] ?? 0,
                    'kd' => $stats['kd']['value'] ?? 0,
                ];
            }
        }

        return null;
    }
}
