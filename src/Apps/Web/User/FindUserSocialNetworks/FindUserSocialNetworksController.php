<?php

declare(strict_types=1);

namespace App\Apps\Web\User\FindUserSocialNetworks;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\FindUserSocialNetworks\FindUserSocialNetworksQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindUserSocialNetworksController extends ApiController
{
    public function __invoke(string $userId): Response
    {
        $query = new FindUserSocialNetworksQuery($userId);

        $response = $this->queryBus->ask($query);

        return $this->collectionResponse($response);
    }
}
