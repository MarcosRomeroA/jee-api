<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FindTournamentBackgroundImageQueryHandler implements QueryHandler
{
    public function __construct(
        private TournamentBackgroundImageFinder $finder,
    ) {
    }

    public function __invoke(FindTournamentBackgroundImageQuery $query): BackgroundImageResponse
    {
        return $this->finder->__invoke(new Uuid($query->tournamentId));
    }
}
