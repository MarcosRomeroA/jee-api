<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\StartMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\StartMatch\MatchStarter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StartMatchController extends ApiController
{
    public function __construct(
        private readonly MatchStarter $matchStarter
    ) {
    }

    #[Route('/api/match/{id}/start', name: 'start_match', methods: ['POST'])]
    public function __invoke(string $id): Response
    {
        $this->matchStarter->start(new Uuid($id));

        return new Response('', Response::HTTP_OK);
    }
}

