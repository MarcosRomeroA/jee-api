<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchMatches;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\SearchMatches\MatchesSearcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SearchMatchesController extends ApiController
{
    public function __construct(
        private readonly MatchesSearcher $matchesSearcher
    ) {
    }

    #[Route('/api/tournament/{tournamentId}/matches', name: 'search_matches', methods: ['GET'])]
    public function __invoke(string $tournamentId, Request $request): Response
    {
        $input = SearchMatchesRequest::fromHttp($request, $tournamentId);
        $this->validateRequest($input);

        $matches = $input->round !== null
            ? $this->matchesSearcher->searchByTournamentAndRound(new Uuid($input->tournamentId), $input->round)
            : $this->matchesSearcher->searchByTournament(new Uuid($input->tournamentId));

        $result = [];
        foreach ($matches as $match) {
            // ...existing code...
        }

        return new JsonResponse($result);
    }
}

