<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Search;

use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Application\Shared\NotificationResponse;
use App\Contexts\Web\Notification\Application\Shared\NotificationCollectionResponse;

final readonly class NotificationSearcher
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    public function __invoke(array $criteria): NotificationCollectionResponse
    {
        $notifications = $this->repository->searchByCriteria($criteria);

        $response = [];
        foreach ($notifications as $notification) {
            $response[] = NotificationResponse::fromEntity($notification);
        }

        return new NotificationCollectionResponse($response);
    }
}

