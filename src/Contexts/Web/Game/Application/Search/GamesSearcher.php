<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Search;

use App\Contexts\Web\Game\Domain\GameRepository;

final class GamesSearcher
{
    public function __construct(
        private readonly GameRepository $repository
    ) {
    }

    public function search(?string $query): array
    {
        if ($query === null || $query === '') {
            return $this->repository->findAll();
        }

        return $this->repository->search($query);
    }
}

