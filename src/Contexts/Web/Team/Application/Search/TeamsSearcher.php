<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Search;

use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TeamsSearcher
{
    public function __construct(
        private readonly TeamRepository $repository
    ) {
    }

    public function search(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $creatorId,
        ?Uuid $userId,
        ?Uuid $tournamentId,
        int $limit,
        int $offset
    ): array {
        return $this->repository->searchWithPagination($query, $gameId, $creatorId, $userId, $tournamentId, $limit, $offset);
    }

    public function count(?string $query, ?Uuid $gameId, ?Uuid $creatorId, ?Uuid $userId, ?Uuid $tournamentId): int
    {
        return $this->repository->countSearch($query, $gameId, $creatorId, $userId, $tournamentId);
    }
}
