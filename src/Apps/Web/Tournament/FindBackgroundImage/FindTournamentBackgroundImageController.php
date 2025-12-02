<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\FindBackgroundImage;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\FindBackgroundImage\FindTournamentBackgroundImageQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindTournamentBackgroundImageController extends ApiController
{
    public function __invoke(string $tournamentId): Response
    {
        $query = new FindTournamentBackgroundImageQuery($tournamentId);
        $response = $this->ask($query);

        return $this->successResponse($response);
    }
}
