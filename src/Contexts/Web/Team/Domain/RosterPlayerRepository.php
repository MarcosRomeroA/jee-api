<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface RosterPlayerRepository
{
    public function save(RosterPlayer $rosterPlayer): void;

    /**
     * @throws \App\Contexts\Web\Team\Domain\Exception\RosterPlayerNotFoundException
     */
    public function findById(Uuid $id): RosterPlayer;

    /**
     * @return array<RosterPlayer>
     */
    public function findByRosterId(Uuid $rosterId): array;

    public function findByRosterAndPlayer(Uuid $rosterId, Uuid $playerId): ?RosterPlayer;

    public function delete(RosterPlayer $rosterPlayer): void;

    public function countStartersByRosterId(Uuid $rosterId): int;

    public function existsLeaderInRoster(Uuid $rosterId): bool;

    public function existsById(Uuid $id): bool;
}
