<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;

interface TeamRepository
{
    public function save(Team $team): void;

    /**
     * @param Uuid $id
     * @return Team
     */
    public function findById(Uuid $id): Team;

    /**
     * @param Uuid $creatorId
     * @return array<Team>
     */
    public function findByCreatorId(Uuid $creatorId): array;

    /**
     * @param string $query
     * @return array<Team>
     */
    public function search(string $query): array;

    public function delete(Team $team): void;

    public function existsById(Uuid $id): bool;

    /**
     * @param string|null $query
     * @param Uuid|null $gameId
     * @param Uuid|null $creatorId
     * @param int $limit
     * @param int $offset
     * @return array<Team>
     */
    public function searchWithPagination(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $creatorId,
        int $limit,
        int $offset
    ): array;

    public function countSearch(?string $query, ?Uuid $gameId, ?Uuid $creatorId): int;
}
