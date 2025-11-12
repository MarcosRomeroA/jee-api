<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface GameRepository
{
    public function save(Game $game): void;

    public function findById(Uuid $id): ?Game;

    public function findAll(): array;

    public function search(string $query): array;

    public function existsById(Uuid $id): bool;
}

