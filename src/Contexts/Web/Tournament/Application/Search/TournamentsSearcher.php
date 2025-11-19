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
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $statusId,
        ?Uuid $responsibleId,
        bool $open,
        int $limit,
        int $offset
    ): array {
        return $this->repository->search($name, $gameId, $statusId, $responsibleId, $open, $limit, $offset);
    }

    public function count(?string $name, ?Uuid $gameId, ?Uuid $statusId, ?Uuid $responsibleId, bool $open): int
    {
        return $this->repository->countSearch($name, $gameId, $statusId, $responsibleId, $open);
    }
}
