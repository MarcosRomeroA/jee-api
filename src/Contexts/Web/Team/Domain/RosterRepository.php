<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface RosterRepository
{
    public function save(Roster $roster): void;

    /**
     * @throws \App\Contexts\Web\Team\Domain\Exception\RosterNotFoundException
     */
    public function findById(Uuid $id): Roster;

    /**
     * @return array<Roster>
     */
    public function findByTeamId(Uuid $teamId): array;

    public function delete(Roster $roster): void;

    public function existsById(Uuid $id): bool;
}
