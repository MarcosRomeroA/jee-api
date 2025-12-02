<?php

declare(strict_types=1);

namespace App\Apps\Web\User\FindBackgroundImage;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\FindBackgroundImage\FindUserBackgroundImageQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindUserBackgroundImageController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindUserBackgroundImageQuery($id);
        $response = $this->ask($query);

        return $this->successResponse($response);
    }
}
