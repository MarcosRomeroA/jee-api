<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TeamRequestRepository
{
    public function save(TeamRequest $request): void;

    public function findById(Uuid $id): ?TeamRequest;

    public function findPendingByTeamAndPlayer(
        Uuid $teamId,
        Uuid $playerId,
    ): ?TeamRequest;

    /**
     * @return TeamRequest[]
     */
    public function findPendingByTeam(Uuid $teamId): array;

    /**
     * @return TeamRequest[]
     */
    public function findPendingByPlayer(Uuid $playerId): array;

    /**
     * @return TeamRequest[]
     */
    public function findAllPending(): array;
}
