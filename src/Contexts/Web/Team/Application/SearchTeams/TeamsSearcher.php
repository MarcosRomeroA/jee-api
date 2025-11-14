<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchTeams;

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
        ?Uuid $ownerId,
        int $limit,
        int $offset
    ): array {
        return $this->repository->searchWithPagination($query, $gameId, $ownerId, $limit, $offset);
    }

    public function count(?string $query, ?Uuid $gameId, ?Uuid $ownerId): int
    {
        return $this->repository->countSearch($query, $gameId, $ownerId);
    }
}
