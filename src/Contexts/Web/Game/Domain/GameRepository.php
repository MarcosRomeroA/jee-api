<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface GameRepository
{
    public function save(Game $game): void;

    /**
     * @param Uuid $id
     * @return Game
     */
    public function findById(Uuid $id): Game;

    /**
     * @return array<Game>
     */
    public function findAll(): array;

    /**
     * @param string $query
     * @return array<Game>
     */
    public function search(string $query): array;

    public function existsById(Uuid $id): bool;
}
