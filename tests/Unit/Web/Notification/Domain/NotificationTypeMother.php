<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Notification\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\NotificationType;

final class NotificationTypeMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $name = null
    ): NotificationType {
        return NotificationType::create(
            $id ?? Uuid::random(),
            $name ?? NotificationType::POST_LIKED
        );
    }

    public static function random(): NotificationType
    {
        return self::create();
    }

    public static function postLiked(): NotificationType
    {
        return self::create(name: NotificationType::POST_LIKED);
    }

    public static function postCommented(): NotificationType
    {
        return self::create(name: NotificationType::POST_COMMENTED);
    }

    public static function newFollower(): NotificationType
    {
        return self::create(name: NotificationType::NEW_FOLLOWER);
    }

    public static function newMessage(): NotificationType
    {
        return self::create(name: NotificationType::NEW_MESSAGE);
    }
}
