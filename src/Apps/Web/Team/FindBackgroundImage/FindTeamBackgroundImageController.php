<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\FindBackgroundImage;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Team\Application\FindBackgroundImage\FindTeamBackgroundImageQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindTeamBackgroundImageController extends ApiController
{
    public function __invoke(string $teamId): Response
    {
        $query = new FindTeamBackgroundImageQuery($teamId);
        $response = $this->ask($query);

        return $this->successResponse($response);
    }
}
