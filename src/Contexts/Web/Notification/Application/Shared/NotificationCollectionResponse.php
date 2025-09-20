<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class NotificationCollectionResponse extends Response
{
    /** @var NotificationResponse[] */
    public array $notifications;

    /**
     * @param array<NotificationResponse> $notifications
     */
    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
    }

    public function toArray(): array
    {
        $response['data'] = [];

        foreach($this->notifications as $notification){
            $response['data'][] = $notification->toArray();
        }

        return $response;
    }
}

