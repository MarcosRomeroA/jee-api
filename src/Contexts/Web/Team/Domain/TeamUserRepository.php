<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TeamUserRepository
{
    public function save(TeamUser $teamUser): void;

    public function findById(Uuid $id): ?TeamUser;

    public function findByTeamAndUser(Uuid $teamId, Uuid $userId): ?TeamUser;

    /**
     * @return array<TeamUser>
     */
    public function findByTeam(Uuid $teamId): array;

    /**
     * @return array<TeamUser>
     */
    public function findByUserId(Uuid $userId): array;

    public function delete(TeamUser $teamUser): void;
}
