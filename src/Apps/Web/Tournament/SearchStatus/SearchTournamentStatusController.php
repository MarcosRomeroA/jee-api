<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchStatus;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchTournamentStatusController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = SearchTournamentStatusRequest::fromHttp($request);
        $query = $input->toQuery();
        $response = $this->ask($query);

        return $this->collectionResponse($response);
    }
}
