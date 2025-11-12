<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchMyTournaments;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final class MyTournamentsSearcher
{
    public function __construct(
        private readonly TournamentRepository $repository
    ) {
    }

    public function search(Uuid $responsibleId, ?string $query = null, ?Uuid $gameId = null): array
    {
        return $this->repository->searchByResponsible($responsibleId, $query, $gameId);
    }
}

