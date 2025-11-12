<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\UpdateMatchResult;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\UpdateMatchResult\MatchResultUpdater;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateMatchResultController extends ApiController
{
    public function __construct(
        private readonly MatchResultUpdater $matchResultUpdater
    ) {
    }

    #[Route('/api/match/{id}/result', name: 'update_match_result', methods: ['PUT'])]
    public function __invoke(string $id, UpdateMatchResultRequest $request): Response
    {
        $this->matchResultUpdater->update(
            new Uuid($id),
            $request->scores,
            $request->winnerId
        );

        return new Response('', Response::HTTP_OK);
    }
}

