<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Shared;

final readonly class NotificationCollectionResponse
{
    /** @var NotificationResponse[] */
    public array $notifications;

    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
    }
}

