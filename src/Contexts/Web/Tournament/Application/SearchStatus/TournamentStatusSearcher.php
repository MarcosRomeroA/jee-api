<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchStatus;

use App\Contexts\Web\Tournament\Domain\TournamentStatusRepository;

final readonly class TournamentStatusSearcher
{
    public function __construct(
        private TournamentStatusRepository $repository,
    ) {
    }

    public function search(): array
    {
        return $this->repository->findAll();
    }
}
