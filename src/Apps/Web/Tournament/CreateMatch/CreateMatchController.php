<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\CreateMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\CreateMatch\MatchCreator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CreateMatchController extends ApiController
{
    public function __construct(
        private readonly MatchCreator $matchCreator,
        ...$args
    ) {
        parent::__construct(...$args);
    }

    #[Route('/api/match', name: 'create_match', methods: ['PUT'])]
    public function __invoke(Request $request): Response
    {
        $input = CreateMatchRequest::fromHttp($request);
        $this->validateRequest($input);

        $this->matchCreator->create(
            new Uuid($input->id),
            new Uuid($input->tournamentId),
            $input->round,
            $input->teamIds,
            $input->name,
            $input->getScheduledAtAsDateTime()
        );

        return new Response('', Response::HTTP_OK);
    }
}

