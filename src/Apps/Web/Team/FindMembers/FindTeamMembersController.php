<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\FindMembers;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\FindMembers\FindTeamMembersQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindTeamMembersController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindTeamMembersQuery($id);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response->toArray());
    }
}
