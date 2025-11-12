<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface GameRankRepository
{
    public function save(GameRank $gameRank): void;

    public function findById(Uuid $id): ?GameRank;

    public function findByGameAndName(Uuid $gameId, string $name): ?GameRank;

    public function findByGame(Uuid $gameId): array;
}

