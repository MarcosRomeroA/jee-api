<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;

interface TeamRepository
{
    public function save(Team $team): void;

    public function findById(Uuid $id): Team;

    public function findByOwnerId(Uuid $ownerId): array;

    public function search(string $query): array;

    public function delete(Team $team): void;

    public function existsById(Uuid $id): bool;

    public function searchWithPagination(
        ?string $query,
        ?Uuid $gameId,
        int $limit,
        int $offset
    ): array;

    public function countSearch(?string $query, ?Uuid $gameId): int;

    public function searchMyTeamsWithPagination(
        Uuid $userId,
        ?string $query,
        ?Uuid $gameId,
        int $limit,
        int $offset
    ): array;

    public function countMyTeams(Uuid $userId, ?string $query, ?Uuid $gameId): int;
}
