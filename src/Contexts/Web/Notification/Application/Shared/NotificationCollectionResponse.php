<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class NotificationCollectionResponse extends Response
{
    /** @var NotificationResponse[] */
    public array $notifications;
    public int $limit;
    public int $offset;
    public int $total;

    /**
     * @param array<NotificationResponse> $notifications
     * @param array{limit: int, offset: int} $criteria
     * @param int $total
     */
    public function __construct(array $notifications, array $criteria, int $total = 0)
    {
        $this->notifications = $notifications;
        $this->limit = $criteria["limit"];
        $this->offset = $criteria["offset"];   
        $this->total = $total;  
    }

    public function toArray(): array
    {
        $response['data'] = [];

        foreach($this->notifications as $notification){
            $response['data'][] = $notification->toArray();
        }

        $response['pagination'] = [
            'limit' => $this->limit,
            'offset' => $this->offset,
            'total' => $this->total,
            'count' => count($this->notifications)
        ];

        return $response;
    }
}
