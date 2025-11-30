<?php

declare(strict_types=1);

namespace App\Apps\Web\Game\FindRequirements;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Game\Application\FindRequirements\FindGameRequirementsQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindGameRequirementsController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindGameRequirementsQuery($id);
        $response = $this->ask($query);

        return $this->successResponse($response);
    }
}
