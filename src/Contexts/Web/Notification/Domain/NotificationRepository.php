<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Exception\NotificationNotFoundException;
use App\Contexts\Web\User\Domain\User;

interface NotificationRepository
{
    public function save(Notification $notification): void;

    /**
     * @throws NotificationNotFoundException
     */
    public function findById(Uuid $id): Notification;

    public function findByUser(User $user): array;

    public function findUnreadByUser(User $user): array;

    public function exists(Uuid $id): bool;

    public function markAsRead(Uuid $id): void;

    public function markAllAsReadForUser(User $user): void;

    /**
     * @param array|null $criteria
     * @return array<Notification>
     */
    public function searchByCriteria(?array $criteria): array;
}
