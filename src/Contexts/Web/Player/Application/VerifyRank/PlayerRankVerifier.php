<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Infrastructure\RankVerification\TrackerGg\TrackerGgApiClient;

final readonly class PlayerRankVerifier
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private GameRankRepository $gameRankRepository,
        private TrackerGgApiClient $trackerGgClient,
    ) {
    }

    public function __invoke(Uuid $playerId): void
    {
        $player = $this->playerRepository->findById($playerId);

        // Obtener el juego desde la relación directa del player
        $game = $player->game();
        $gameId = $game->getId();

        // Obtener el accountId del player
        $accountId = $player->accountData()->accountId();
        if ($accountId === null) {
            throw RankVerificationException::invalidAccountFormat('Player has no accountId configured');
        }

        // Llamar a Tracker.gg para obtener el rango
        $statsData = $this->trackerGgClient->getPlayerStats($gameId->value(), $accountId);
        $rankInfo = $this->trackerGgClient->extractRankFromResponse($gameId->value(), $statsData);

        if ($rankInfo === null || $rankInfo['rank'] === null) {
            throw new RankVerificationException(
                'Could not extract rank from API response',
                'rank_extraction_failed',
            );
        }

        // Buscar el GameRank correspondiente en la BD
        $rankName = $this->normalizeRankName($rankInfo['rank']);
        $level = $this->extractLevelFromTier($rankInfo['tier'] ?? null);

        $gameRank = $this->gameRankRepository->findByGameAndRankName($gameId, $rankName, $level);

        if ($gameRank === null) {
            // Intentar sin nivel específico
            $gameRank = $this->gameRankRepository->findByGameAndRankName($gameId, $rankName, null);
        }

        if ($gameRank === null) {
            throw RankVerificationException::rankNotFound($rankName);
        }

        // Actualizar el player con el rango y marcarlo como verificado
        $player->setGameRank($gameRank);
        $player->verify();

        $this->playerRepository->save($player);
    }

    private function normalizeRankName(string $rank): string
    {
        // Tracker.gg puede devolver "Gold 3" o "Gold III", normalizamos
        $rank = trim($rank);

        // Remover números romanos o arábigos al final (ej: "Gold 3" -> "Gold")
        $rank = preg_replace('/\s+[IVX0-9]+$/i', '', $rank);

        return $rank;
    }

    private function extractLevelFromTier(?string $tier): ?int
    {
        if ($tier === null) {
            return null;
        }

        // Convertir tier a nivel (1, 2, 3)
        // Valorant usa "1", "2", "3" o números romanos
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
