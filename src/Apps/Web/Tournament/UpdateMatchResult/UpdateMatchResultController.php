<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\UpdateMatchResult;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\UpdateMatchResult\MatchResultUpdater;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateMatchResultController extends ApiController
{
    public function __construct(
        private readonly MatchResultUpdater $matchResultUpdater,
        ...$args
    ) {
        parent::__construct(...$args);
    }

    #[Route('/api/match/{id}/result', name: 'update_match_result', methods: ['PUT'])]
    public function __invoke(string $id, Request $request): Response
    {
        $input = UpdateMatchResultRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        $this->matchResultUpdater->update(
            new Uuid($input->matchId),
            $input->scores,
            $input->winnerId
        );

        return new Response('', Response::HTTP_OK);
    }
}

