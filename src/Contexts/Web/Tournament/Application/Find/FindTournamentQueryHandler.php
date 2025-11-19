<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentResponse;

final class FindTournamentQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly TournamentFinder $finder,
        private readonly FileManager $fileManager,
    ) {
    }

    public function __invoke(FindTournamentQuery $query): TournamentResponse
    {
        $tournament = $this->finder->find(new Uuid($query->id));
        return TournamentResponse::fromTournament($tournament, $this->fileManager);
    }
}
