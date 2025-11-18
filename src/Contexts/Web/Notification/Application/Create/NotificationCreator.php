<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\Post\Domain\Post;

final readonly class NotificationCreator
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(
        Uuid $id,
        NotificationType $notificationType,
        User $user,
        ?Post $post = null,
    ): void {
        $notification = Notification::create($id, $notificationType, $user, $post);
        $this->notificationRepository->save($notification);
        $this->bus->publish($notification->pullDomainEvents());
    }
}
