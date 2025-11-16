<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Exception\GameRankNotFoundException;

interface GameRankRepository
{
    public function save(GameRank $gameRank): void;

    /**
     * @throws GameRankNotFoundException
     */
    public function findById(Uuid $id): GameRank;

    /**
     * @return GameRank[]
     */
    public function findByGame(Uuid $gameId): array;

    public function existsById(Uuid $id): bool;
}
