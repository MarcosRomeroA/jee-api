<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface NotificationTypeRepository
{
    public function save(NotificationType $notificationType): void;

    public function findById(Uuid $id): ?NotificationType;

    /**
     * @param string $name
     * @return NotificationType
     * 
     * @throws NotificationTypeNotFoundException
     */
    public function findByName(string $name): ?NotificationType;

    public function exists(Uuid $id): bool;

    public function existsByName(string $name): bool;

    public function searchAll(): array;

    public function delete(Uuid $id): void;
}
