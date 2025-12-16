<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TournamentRepository
{
    public function save(Tournament $tournament): void;

    /**
     * @param Uuid $id
     * @return Tournament
     */
    public function findById(Uuid $id): Tournament;

    public function delete(Tournament $tournament): void;

    public function existsById(Uuid $id): bool;

    /**
     * @param string|null $name
     * @param Uuid|null $gameId
     * @param Uuid|null $statusId
     * @param Uuid|null $responsibleId
     * @param bool $open
     * @param int $limit
     * @param int $offset
     * @param bool $upcoming
     * @param Uuid|null $excludeUserId
     * @return array<Tournament>
     */
    public function search(
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $statusId,
        ?Uuid $responsibleId,
        bool $open,
        int $limit,
        int $offset,
        bool $upcoming = false,
        ?Uuid $excludeUserId = null,
    ): array;

    public function countSearch(
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $statusId,
        ?Uuid $responsibleId,
        bool $open,
        bool $upcoming = false,
        ?Uuid $excludeUserId = null,
    ): int;

    /**
     * @param array $criteria
     * @return array<Tournament>
     */
    public function searchByCriteria(array $criteria): array;

    public function countByCriteria(array $criteria): int;

    /**
     * @return array<Tournament>
     */
    public function findWonTournaments(
        int $limit,
        int $offset,
        ?Uuid $userId = null,
        ?Uuid $teamId = null,
        ?Uuid $tournamentId = null,
    ): array;

    public function countWonTournaments(
        ?Uuid $userId = null,
        ?Uuid $teamId = null,
        ?Uuid $tournamentId = null,
    ): int;
}
