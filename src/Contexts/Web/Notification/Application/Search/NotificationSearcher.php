<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Search;

use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Application\Shared\NotificationResponse;
use App\Contexts\Web\Notification\Application\Shared\NotificationCollectionResponse;

final readonly class NotificationSearcher
{
    public function __construct(
        private NotificationRepository $repository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(?array $criteria): NotificationCollectionResponse
    {
        $notifications = $this->repository->searchByCriteria($criteria);

        $response = [];
        foreach ($notifications as $notification) {
            $profileImage = null;
            $user = $notification->getUser();
            if ($user !== null) {
                $profileImage = $user->getAvatarUrl(128, $this->cdnBaseUrl);
            }

            $response[] = NotificationResponse::fromEntity($notification, $profileImage);
        }

        $total = $this->repository->countByCriteria($criteria);

        return new NotificationCollectionResponse($response, $criteria, $total);
    }
}
