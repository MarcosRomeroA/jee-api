<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\DeleteMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Tournament\Application\DeleteMatch\MatchDeleter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteMatchController extends ApiController
{
    public function __construct(
        private readonly MatchDeleter $matchDeleter
    ) {
    }

    #[Route('/api/match/{id}', name: 'delete_match', methods: ['DELETE'])]
    public function __invoke(string $id): Response
    {
        $this->matchDeleter->delete(new Uuid($id));

        return new Response('', Response::HTTP_OK);
    }
}

