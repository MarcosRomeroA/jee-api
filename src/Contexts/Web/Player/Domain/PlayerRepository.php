<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface PlayerRepository
{
    public function save(Player $player): void;

    /**
     * @param Uuid $id
     * @return Player
     */
    public function findById(Uuid $id): Player;

    /**
     * @param Uuid $userId
     * @return array<Player>
     */
    public function findByUserId(Uuid $userId): array;

    /**
     * @param Uuid $gameId
     * @return array<Player>
     */
    public function findByGameId(Uuid $gameId): array;

    /**
     * @return array<Player>
     */
    public function findAll(): array;

    public function delete(Player $player): void;

    public function existsById(Uuid $id): bool;

    /**
     * @param string|null $query
     * @param Uuid|null $gameId
     * @param Uuid|null $teamId
     * @param Uuid|null $userId
     * @param int $limit
     * @param int $offset
     * @return array<Player>
     */
    public function searchWithPagination(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $teamId,
        ?Uuid $userId,
        int $limit,
        int $offset
    ): array;

    public function countSearch(?string $query, ?Uuid $gameId, ?Uuid $teamId, ?Uuid $userId): int;

    public function existsByUserIdAndGameId(
        Uuid $userId,
        Uuid $gameId
    ): bool;

    public function countByUserId(Uuid $userId): int;

    /**
     * Find a player by Steam ID, game and user (excluding a specific player ID)
     * Used to prevent same user from having duplicate accounts for the same game
     */
    public function findBySteamIdAndGameForUser(string $steamId, Uuid $gameId, Uuid $userId, ?Uuid $excludePlayerId = null): ?Player;

    /**
     * Find a player by Riot username, tag, game and user (excluding a specific player ID)
     * Used to prevent same user from having duplicate accounts for the same game
     */
    public function findByRiotAccountAndGameForUser(string $username, string $tag, Uuid $gameId, Uuid $userId, ?Uuid $excludePlayerId = null): ?Player;
}
