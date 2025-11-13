<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchMyTeams;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRepository;

final class MyTeamsSearcher
{
    public function __construct(
        private readonly TeamRepository $repository
    ) {
    }

    public function search(
        Uuid $userId,
        ?string $query,
        ?Uuid $gameId,
        int $limit,
        int $offset
    ): array {
        return $this->repository->searchMyTeamsWithPagination(
            $userId,
            $query,
            $gameId,
            $limit,
            $offset
        );
    }

    public function count(Uuid $userId, ?string $query, ?Uuid $gameId): int
    {
        return $this->repository->countMyTeams($userId, $query, $gameId);
    }
}
