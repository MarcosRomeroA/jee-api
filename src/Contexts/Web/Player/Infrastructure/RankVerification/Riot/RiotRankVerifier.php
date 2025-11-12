<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\Riot;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\Service\RankVerifier;

/**
 * Implementación de RankVerifier para Riot Games API
 * Soporta: League of Legends, Valorant, TFT
 */
final class RiotRankVerifier implements RankVerifier
{
    // Mapeo de game IDs a identificadores
    private const GAME_IDENTIFIERS = [
        'league-of-legends' => 'lol',
        'valorant' => 'valorant',
        'tft' => 'tft',
    ];

    public function __construct(
        private readonly RiotApiClient $riotApiClient
    ) {
    }

    public function verify(Player $player, Uuid $gameId): bool
    {
        try {
            // Obtener identificador del juego (esto debería venir de la entidad Game)
            // Por ahora asumimos que es League of Legends
            $gameIdentifier = 'lol';

            $rankInfo = $this->getRankInfo($player->username(), $gameIdentifier);

            return !empty($rankInfo);
        } catch (RankVerificationException $e) {
            // Si el jugador no se encuentra, no está verificado
            if ($e->getMessage() === 'player_not_found_in_api') {
                return false;
            }
            throw $e;
        }
    }

    public function getRankInfo(string $username, string $gameIdentifier): array
    {
        return match ($gameIdentifier) {
            'lol' => $this->getLeagueOfLegendsRank($username),
            'valorant' => $this->getValorantRank($username),
            'tft' => $this->getTFTRank($username),
            default => throw new RankVerificationException("Game $gameIdentifier not supported by Riot API")
        };
    }

    /**
     * Obtiene rango de League of Legends
     */
    private function getLeagueOfLegendsRank(string $summonerName): array
    {
        // 1. Obtener información del summoner
        $summoner = $this->riotApiClient->getSummonerByName($summonerName);

        if (empty($summoner)) {
            throw RankVerificationException::playerNotFound($summonerName);
        }

        // 2. Obtener entradas de liga
        $leagueEntries = $this->riotApiClient->getLeagueEntriesBySummonerId($summoner['id']);

        if (empty($leagueEntries)) {
            return [
                'rank' => 'UNRANKED',
                'tier' => 'UNRANKED',
                'points' => 0,
                'wins' => 0,
                'losses' => 0,
            ];
        }

        // Buscar entrada de Ranked Solo/Duo
        $soloQueue = null;
        foreach ($leagueEntries as $entry) {
            if ($entry['queueType'] === 'RANKED_SOLO_5x5') {
                $soloQueue = $entry;
                break;
            }
        }

        if ($soloQueue === null) {
            // Si no tiene Solo/Duo, buscar Flex
            foreach ($leagueEntries as $entry) {
                if ($entry['queueType'] === 'RANKED_FLEX_SR') {
                    $soloQueue = $entry;
                    break;
                }
            }
        }

        if ($soloQueue === null) {
            return [
                'rank' => 'UNRANKED',
                'tier' => 'UNRANKED',
                'points' => 0,
                'wins' => 0,
                'losses' => 0,
            ];
        }

        return [
            'rank' => $soloQueue['tier'] ?? 'UNRANKED',
            'tier' => $soloQueue['rank'] ?? '',
            'points' => $soloQueue['leaguePoints'] ?? 0,
            'wins' => $soloQueue['wins'] ?? 0,
            'losses' => $soloQueue['losses'] ?? 0,
            'queueType' => $soloQueue['queueType'] ?? '',
        ];
    }

    /**
     * Obtiene rango de Valorant
     */
    private function getValorantRank(string $riotId): array
    {
        // Separar nombre#tag
        $parts = explode('#', $riotId);
        if (count($parts) !== 2) {
            throw new RankVerificationException("Invalid Riot ID format. Expected: Name#TAG");
        }

        [$gameName, $tagLine] = $parts;

        // 1. Obtener cuenta por Riot ID
        $account = $this->riotApiClient->getAccountByRiotId($gameName, $tagLine);

        if (empty($account)) {
            throw RankVerificationException::playerNotFound($riotId);
        }

        // 2. Obtener información de ranked
        try {
            $ranked = $this->riotApiClient->getValorantRankByPuuid($account['puuid']);

            // Valorant API puede no estar disponible en todas las regiones
            return [
                'rank' => $ranked['currentTier'] ?? 'UNRANKED',
                'tier' => $ranked['currentTierPatched'] ?? 'UNRANKED',
                'rr' => $ranked['ranking_in_tier'] ?? 0, // RR = Ranked Rating
            ];
        } catch (RankVerificationException $e) {
            // Si la API no está disponible, devolver información básica
            return [
                'rank' => 'API_NOT_AVAILABLE',
                'tier' => 'Cannot verify Valorant rank',
                'rr' => 0,
            ];
        }
    }

    /**
     * Obtiene rango de TFT (Teamfight Tactics)
     */
    private function getTFTRank(string $summonerName): array
    {
        // TFT usa la misma API que LoL para summoners
        $summoner = $this->riotApiClient->getSummonerByName($summonerName);

        if (empty($summoner)) {
            throw RankVerificationException::playerNotFound($summonerName);
        }

        $leagueEntries = $this->riotApiClient->getLeagueEntriesBySummonerId($summoner['id']);

        // Buscar entrada de TFT Ranked
        $tftRanked = null;
        foreach ($leagueEntries as $entry) {
            if ($entry['queueType'] === 'RANKED_TFT') {
                $tftRanked = $entry;
                break;
            }
        }

        if ($tftRanked === null) {
            return [
                'rank' => 'UNRANKED',
                'tier' => 'UNRANKED',
                'points' => 0,
            ];
        }

        return [
            'rank' => $tftRanked['tier'] ?? 'UNRANKED',
            'tier' => $tftRanked['rank'] ?? '',
            'points' => $tftRanked['leaguePoints'] ?? 0,
        ];
    }
}

