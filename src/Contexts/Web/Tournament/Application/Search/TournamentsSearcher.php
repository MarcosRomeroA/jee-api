<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Search;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final class TournamentsSearcher
{
    public function __construct(
        private readonly TournamentRepository $repository
    ) {
    }

    public function search(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $responsibleId,
        bool $open,
        int $limit,
        int $offset
    ): array {
        return $this->repository->search($query, $gameId, $responsibleId, $open, $limit, $offset);
    }

    public function count(?string $query, ?Uuid $gameId, ?Uuid $responsibleId, bool $open): int
    {
        return $this->repository->countSearch($query, $gameId, $responsibleId, $open);
    }
}

