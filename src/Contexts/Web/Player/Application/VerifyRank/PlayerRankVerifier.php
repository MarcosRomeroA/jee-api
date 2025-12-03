<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Infrastructure\RankVerification\Henrik\HenrikApiClient;
use App\Contexts\Web\Player\Infrastructure\RankVerification\OpenDota\OpenDotaApiClient;
use App\Contexts\Web\Player\Infrastructure\RankVerification\Riot\RiotApiClient;
use App\Contexts\Web\Player\Infrastructure\RankVerification\TrackerGg\TrackerGgApiClient;

final readonly class PlayerRankVerifier
{
    // Game IDs mapeados a proveedores
    private const GAME_PROVIDERS = [
        '550e8400-e29b-41d4-a716-446655440080' => 'valorant',  // Valorant -> Henrik API
        '550e8400-e29b-41d4-a716-446655440081' => 'lol',       // LoL -> Riot API
        '550e8400-e29b-41d4-a716-446655440082' => 'cs2',       // CS2 -> Tracker.gg
        '550e8400-e29b-41d4-a716-446655440083' => 'dota2',     // Dota 2 -> OpenDota
    ];

    public function __construct(
        private PlayerRepository $playerRepository,
        private GameRankRepository $gameRankRepository,
        private HenrikApiClient $henrikClient,
        private RiotApiClient $riotClient,
        private TrackerGgApiClient $trackerGgClient,
        private OpenDotaApiClient $openDotaClient,
    ) {
    }

    public function __invoke(Uuid $playerId): void
    {
        $player = $this->playerRepository->findById($playerId);
        $game = $player->game();
        $gameId = $game->getId()->value();
        $accountData = $player->accountData();

        $provider = self::GAME_PROVIDERS[$gameId] ?? null;

        if ($provider === null) {
            throw RankVerificationException::gameNotSupported($gameId);
        }

        $rankInfo = match ($provider) {
            'valorant' => $this->verifyValorant($accountData),
            'lol' => $this->verifyLoL($accountData),
            'cs2' => $this->verifyCS2($accountData),
            'dota2' => $this->verifyDota2($accountData),
            default => throw RankVerificationException::gameNotSupported($provider),
        };

        if ($rankInfo['rank'] === null) {
            throw new RankVerificationException(
                'Could not extract rank from API response',
                'rank_extraction_failed',
            );
        }

        // Buscar el GameRank correspondiente en la BD
        $rankName = $this->normalizeRankName($rankInfo['rank']);

        // Si el jugador no tiene rango (Unrated, Unranked, etc.), asignar el GameRank "Unranked" del juego
        if ($this->isUnranked($rankName)) {
            $unrankedGameRank = $this->gameRankRepository->findByGameAndRankName(
                $game->getId(),
                'Unranked',
                null
            );
            $player->setGameRank($unrankedGameRank);
            $player->verify();
            $this->playerRepository->save($player);
            return;
        }

        $level = $this->extractLevel($rankInfo['tier'] ?? null);

        $gameRank = $this->gameRankRepository->findByGameAndRankName(
            $game->getId(),
            $rankName,
            $level
        );

        if ($gameRank === null) {
            // Intentar sin nivel específico
            $gameRank = $this->gameRankRepository->findByGameAndRankName(
                $game->getId(),
                $rankName,
                null
            );
        }

        if ($gameRank === null) {
            throw RankVerificationException::rankNotFound($rankName);
        }

        // Actualizar el player con el rango y marcarlo como verificado
        $player->setGameRank($gameRank);
        $player->verify();

        $this->playerRepository->save($player);
    }

    /**
     * Verifica rango de Valorant usando Henrik API
     */
    private function verifyValorant($accountData): array
    {
        $region = $accountData->region();
        $username = $accountData->username();
        $tag = $accountData->tag();

        if ($region === null || $username === null || $tag === null) {
            throw RankVerificationException::invalidAccountFormat(
                'Valorant requires region, username, and tag'
            );
        }

        $mmrData = $this->henrikClient->getMMR($region, $username, $tag);

        return $this->henrikClient->extractRankInfo($mmrData);
    }

    /**
     * Verifica rango de LoL usando Riot API
     */
    private function verifyLoL($accountData): array
    {
        $region = $accountData->region();
        $username = $accountData->username();
        $tag = $accountData->tag();

        if ($region === null || $username === null || $tag === null) {
            throw RankVerificationException::invalidAccountFormat(
                'League of Legends requires region, username, and tag'
            );
        }

        return $this->riotClient->getLoLRank($region, $username, $tag);
    }

    /**
     * Verifica rango de CS2 usando Tracker.gg API
     */
    private function verifyCS2($accountData): array
    {
        $steamId = $accountData->steamId();

        if ($steamId === null) {
            throw RankVerificationException::invalidAccountFormat(
                'CS2 requires steamId'
            );
        }

        $statsData = $this->trackerGgClient->getCS2Stats($steamId);

        return $this->trackerGgClient->extractCS2Rank($statsData);
    }

    /**
     * Verifica rango de Dota 2 usando OpenDota API
     */
    private function verifyDota2($accountData): array
    {
        $steamId = $accountData->steamId();

        if ($steamId === null) {
            throw RankVerificationException::invalidAccountFormat(
                'Dota 2 requires steamId'
            );
        }

        // Convertir Steam ID64 a Account ID (32-bit) para OpenDota
        $accountId = $this->openDotaClient->steamId64ToAccountId($steamId);

        $playerData = $this->openDotaClient->getPlayer($accountId);

        return $this->openDotaClient->extractRankInfo($playerData);
    }

    private function normalizeRankName(string $rank): string
    {
        $rank = trim($rank);

        // Remover números al final (ej: "Gold 3" -> "Gold")
        $rank = preg_replace('/\s+\d+$/i', '', $rank);

        return $rank;
    }

    /**
     * Check if the rank indicates the player has no competitive rank
     */
    private function isUnranked(string $rankName): bool
    {
        $unrankedTerms = [
            'unrated',
            'unranked',
            'none',
            'n/a',
            'uncalibrated',
        ];

        return in_array(strtolower($rankName), $unrankedTerms, true);
    }

    private function extractLevel(?string $tier): ?int
    {
        if ($tier === null) {
            return null;
        }

        $tier = trim($tier);

        // Si es número directo
        if (is_numeric($tier)) {
            return (int) $tier;
        }

        // Si es número romano
        $romanNumerals = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5];
        $upperTier = strtoupper($tier);

        return $romanNumerals[$upperTier] ?? null;
    }
}
