<?php

declare(strict_types=1);

namespace App\Apps\Web\User\SocialNetwork;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\SearchSocialNetworks\SearchSocialNetworksQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchSocialNetworksController extends ApiController
{
    public function __invoke(Request $request, string $sessionId): Response
    {
        $mine = filter_var($request->query->get('mine', 'false'), FILTER_VALIDATE_BOOLEAN);

        $query = new SearchSocialNetworksQuery($sessionId, $mine);

        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
