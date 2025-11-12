<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TournamentStatusRepository
{
    public function save(TournamentStatus $status): void;
    public function findById(Uuid $id): ?TournamentStatus;
    public function findByName(string $name): ?TournamentStatus;
    public function findAll(): array;
}

