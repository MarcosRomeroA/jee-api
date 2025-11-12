<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchOpenTournaments;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final class OpenTournamentsSearcher
{
    public function __construct(
        private readonly TournamentRepository $repository
    ) {
    }

    public function search(?string $query = null, ?Uuid $gameId = null): array
    {
        return $this->repository->searchOpen($query, $gameId);
    }
}

