<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface EventRepository
{
    public function save(Event $event): void;

    public function findById(Uuid $id): ?Event;

    public function delete(Event $event): void;

    public function existsById(Uuid $id): bool;

    /**
     * Search upcoming events ordered by startAt (closest first)
     *
     * @return array<Event>
     */
    public function searchUpcoming(
        ?Uuid $gameId,
        ?EventType $type,
        int $limit,
        int $offset,
    ): array;

    /**
     * Count upcoming events
     */
    public function countUpcoming(
        ?Uuid $gameId,
        ?EventType $type,
    ): int;
}
