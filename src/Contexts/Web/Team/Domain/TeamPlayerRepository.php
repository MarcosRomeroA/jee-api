<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TeamPlayerRepository
{
    public function save(TeamPlayer $teamPlayer): void;

    public function findById(Uuid $id): ?TeamPlayer;

    public function findByTeamAndPlayer(Uuid $teamId, Uuid $playerId): ?TeamPlayer;

    /**
     * @param Uuid $teamId
     * @return array<TeamPlayer>
     */
    public function findByTeam(Uuid $teamId): array;

    /**
     * @param Uuid $playerId
     * @return array<TeamPlayer>
     */
    public function findByPlayerId(Uuid $playerId): array;

    public function delete(TeamPlayer $teamPlayer): void;
}
