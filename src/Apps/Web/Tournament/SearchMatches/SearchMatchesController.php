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
        $round = $request->query->get('round');

        $matches = $round !== null
            ? $this->matchesSearcher->searchByTournamentAndRound(new Uuid($tournamentId), (int) $round)
            : $this->matchesSearcher->searchByTournament(new Uuid($tournamentId));

        $result = [];
        foreach ($matches as $match) {
            $participants = [];
            foreach ($match->participants() as $participant) {
                $participants[] = [
                    'teamId' => $participant->team()->id()->value(),
                    'teamName' => $participant->team()->name(),
                    'score' => $participant->score()?->value(),
                    'isWinner' => $participant->isWinner(),
                ];
            }

            $result[] = [
                'id' => $match->id()->value(),
                'name' => $match->name(),
                'round' => $match->round(),
                'status' => $match->status(),
                'scheduledAt' => $match->scheduledAt()?->format('Y-m-d\TH:i:s\Z'),
                'participants' => $participants,
            ];
        }

        return new JsonResponse($result);
    }
}

