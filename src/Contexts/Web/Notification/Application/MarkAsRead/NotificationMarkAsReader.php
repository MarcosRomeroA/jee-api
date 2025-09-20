<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\MarkAsRead;

use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\Exception\NotificationNotBelongsToUser;

final readonly class NotificationMarkAsReader
{
    public function __construct(
        private NotificationRepository $notificationRepository
    )
    {
    }

    public function __invoke(Uuid $notificationId, Uuid $sessionId): void
    {
        $notification = $this->notificationRepository->findById($notificationId);

        if (!$notification->getUserToNotify()->getId()->equals($sessionId)) {
            throw new UnauthorizedException();
        }

        if (!$notification->getIsRead()) {
            $notification->markAsRead();
            $this->notificationRepository->save($notification);
        }
    }
}
