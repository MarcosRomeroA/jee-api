<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Exception\GameRoleNotFoundException;

interface GameRoleRepository
{
    public function save(GameRole $gameRole): void;

    /**
     * @param Uuid $id
     * @return GameRole
     * @throws GameRoleNotFoundException
     */
    public function findById(Uuid $id): GameRole;

    /**
     * @param Uuid $gameId
     * @return array<GameRole>
     */
    public function findByGame(Uuid $gameId): array;

    public function existsById(Uuid $id): bool;
}
