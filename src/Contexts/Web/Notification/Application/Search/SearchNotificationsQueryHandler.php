<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Notification\Application\Shared\NotificationCollectionResponse;
use Exception;

final readonly class SearchNotificationsQueryHandler implements QueryHandler
{
    public function __construct(
        private NotificationSearcher $searcher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SearchNotificationsQuery $query): NotificationCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria);
    }
}

