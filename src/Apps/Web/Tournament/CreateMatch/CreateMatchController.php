<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\CreateMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\CreateMatch\MatchCreator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CreateMatchController extends ApiController
{
    public function __construct(
        private readonly MatchCreator $matchCreator
    ) {
    }

    #[Route('/api/match', name: 'create_match', methods: ['PUT'])]
    public function __invoke(CreateMatchRequest $request): Response
    {
        $this->matchCreator->create(
            new Uuid($request->id),
            new Uuid($request->tournamentId),
            $request->round,
            $request->teamIds,
            $request->name,
            $request->getScheduledAtAsDateTime()
        );

        return new Response('', Response::HTTP_OK);
    }
}

