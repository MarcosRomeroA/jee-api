<?php

declare(strict_types=1);

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
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $statusId,
        ?Uuid $responsibleId,
        bool $open,
        int $limit,
        int $offset,
        bool $upcoming = false,
        ?Uuid $excludeUserId = null,
    ): array {
        return $this->repository->search($name, $gameId, $statusId, $responsibleId, $open, $limit, $offset, $upcoming, $excludeUserId);
    }

    public function count(
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $statusId,
        ?Uuid $responsibleId,
        bool $open,
        bool $upcoming = false,
        ?Uuid $excludeUserId = null,
    ): int {
        return $this->repository->countSearch($name, $gameId, $statusId, $responsibleId, $open, $upcoming, $excludeUserId);
    }
}
