<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Shared;

use App\Contexts\Web\Notification\Domain\Notification;

final class NotificationResponse
{
    public function __construct(
        public string $id,
        public string $type,
        public ?string $userId,
        public ?string $username,
        public ?string $postId,
        public ?string $message,
        public string $date,
    ) {}

    public static function fromEntity(Notification $notification): self
    {
        return new self(
            $notification->getId()->value(),
            $notification->getNotificationType()->getName(),
            $notification->getUser()?->getId()?->value(),
            $notification->getUser()?->getUsername()?->value(),
            $notification->getPost()?->getId()?->value(),
            $notification->getMessage()?->getContent()->value(),
            $notification->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
