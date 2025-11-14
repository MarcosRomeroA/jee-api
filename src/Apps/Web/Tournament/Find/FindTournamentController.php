<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Find;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\Find\FindTournamentQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindTournamentController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $input = FindTournamentRequest::fromId($id);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $tournamentResponse = $this->queryBus->ask($query);

        return $this->successResponse($tournamentResponse->toArray());
    }
}

