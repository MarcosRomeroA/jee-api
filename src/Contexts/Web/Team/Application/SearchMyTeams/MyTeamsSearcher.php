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

    public function search(Uuid $userId, ?string $query = null): array
    {
        return $this->repository->searchByUser($userId, $query);
    }
}

