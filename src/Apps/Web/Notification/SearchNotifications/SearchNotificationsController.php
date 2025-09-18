<?php declare(strict_types=1);

namespace App\Apps\Web\Notification\SearchNotifications;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Notification\Application\Search\SearchNotificationsQuery;
use Symfony\Component\HttpFoundation\Response;

final class SearchNotificationsController extends ApiController
{
    public function __invoke(SearchNotificationsRequest $request): Response
    {
        $criteria = $request->q;

        $query = new SearchNotificationsQuery($criteria);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
