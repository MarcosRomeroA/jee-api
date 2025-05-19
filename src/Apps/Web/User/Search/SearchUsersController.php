<?php declare(strict_types=1);

namespace App\Apps\Web\User\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\Search\SearchUsersQuery;
use Symfony\Component\HttpFoundation\Response;

final class SearchUsersController extends ApiController
{
    public function __invoke(SearchUsersRequest $request): Response
    {
        $criteria = $request->q;

        $query = new SearchUsersQuery($criteria);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}