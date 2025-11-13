<?php declare(strict_types=1);

namespace App\Apps\Web\User\FindTournaments;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\FindTournaments\FindUserTournamentsQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindUserTournamentsController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new FindUserTournamentsQuery($id);
        $tournaments = $this->queryBus->ask($query);

        return $this->successResponse($tournaments);
    }
}

