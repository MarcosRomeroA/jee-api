<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TournamentRepository
{
    public function save(Tournament $tournament): void;

    public function findById(Uuid $id): Tournament;

    public function delete(Tournament $tournament): void;

    public function existsById(Uuid $id): bool;

    public function search(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $responsibleId,
        bool $open,
        int $limit,
        int $offset
    ): array;

    public function countSearch(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $responsibleId,
        bool $open
    ): int;
}

