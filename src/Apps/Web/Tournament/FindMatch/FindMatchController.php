<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\FindMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\FindMatch\MatchFinder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FindMatchController extends ApiController
{
    public function __construct(
        private readonly MatchFinder $matchFinder
    ) {
    }

    #[Route('/api/match/{id}', name: 'find_match', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        $match = $this->matchFinder->find(new Uuid($id));

        $participants = [];
        foreach ($match->participants() as $participant) {
            $participants[] = [
                'id' => $participant->id()->value(),
                'teamId' => $participant->team()->id()->value(),
                'teamName' => $participant->team()->name(),
                'score' => $participant->score()?->value(),
                'position' => $participant->position(),
                'isWinner' => $participant->isWinner(),
            ];
        }

        return new JsonResponse([
            'id' => $match->id()->value(),
            'tournamentId' => $match->tournament()->id()->value(),
            'name' => $match->name(),
            'round' => $match->round(),
            'status' => $match->status(),
            'scheduledAt' => $match->scheduledAt()?->format('Y-m-d\TH:i:s\Z'),
            'startedAt' => $match->startedAt()?->format('Y-m-d\TH:i:s\Z'),
            'completedAt' => $match->completedAt()?->format('Y-m-d\TH:i:s\Z'),
            'participants' => $participants,
        ]);
    }
}

