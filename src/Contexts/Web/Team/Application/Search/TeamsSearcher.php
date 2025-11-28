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
        ?string $name,
        ?Uuid $gameId,
        ?Uuid $creatorId,
        ?Uuid $userId,
        ?Uuid $tournamentId,
        ?Uuid $ownerOrLeaderId,
        ?Uuid $myCreatorId,
        ?Uuid $myLeaderId,
        int $limit,
        int $offset
    ): array {
        return $this->repository->searchWithPagination($name, $gameId, $creatorId, $userId, $tournamentId, $ownerOrLeaderId, $myCreatorId, $myLeaderId, $limit, $offset);
    }

    public function count(?string $name, ?Uuid $gameId, ?Uuid $creatorId, ?Uuid $userId, ?Uuid $tournamentId, ?Uuid $ownerOrLeaderId, ?Uuid $myCreatorId, ?Uuid $myLeaderId): int
    {
        return $this->repository->countSearch($name, $gameId, $creatorId, $userId, $tournamentId, $ownerOrLeaderId, $myCreatorId, $myLeaderId);
    }
}
