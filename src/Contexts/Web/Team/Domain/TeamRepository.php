<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;

interface TeamRepository
{
    public function save(Team $team): void;

    public function findById(Uuid $id): Team;

    public function findByCreatorId(Uuid $creatorId): array;

    public function search(string $query): array;

    public function delete(Team $team): void;

    public function existsById(Uuid $id): bool;

    public function searchWithPagination(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $creatorId,
        int $limit,
        int $offset
    ): array;

    public function countSearch(?string $query, ?Uuid $gameId, ?Uuid $creatorId): int;
}
