<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Notification\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\User\Domain\User;

final class NotificationMother
{
    public static function create(
        ?Uuid $id = null,
        ?NotificationType $notificationType = null,
        ?User $userToNotify = null,
        ?User $user = null,
        ?Post $post = null
    ): Notification {
        return Notification::create(
            $id ?? Uuid::random(),
            $notificationType ?? NotificationTypeMother::random(),
            $userToNotify ?? UserMother::random(),
            $user,
            $post,
            null
        );
    }

    public static function random(): Notification
    {
        return self::create();
    }
}
