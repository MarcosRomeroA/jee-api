<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;

interface PlayerRepository
{
    public function save(Player $player): void;

    public function findById(Uuid $id): Player;

    public function findByUserId(Uuid $userId): array;

    public function findByGameId(Uuid $gameId): array;

    public function findAll(): array;

    public function delete(Player $player): void;

    public function existsById(Uuid $id): bool;

    public function searchWithPagination(
        ?string $query,
        ?Uuid $gameId,
        ?Uuid $userId,
        int $limit,
        int $offset
    ): array;

    public function countSearch(?string $query, ?Uuid $gameId, ?Uuid $userId): int;

    public function existsByUserIdAndUsernameAndGameId(
        Uuid $userId,
        UsernameValue $username,
        Uuid $gameId
    ): bool;
}
