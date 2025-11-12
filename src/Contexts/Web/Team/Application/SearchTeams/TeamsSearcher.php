<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchTeams;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamsSearcher
{
    public function __construct(
        private TeamRepository $repository
    ) {
    }

    public function search(?string $query = null, ?Uuid $gameId = null): array
    {
        return $this->repository->search($query, $gameId);
    }
}

