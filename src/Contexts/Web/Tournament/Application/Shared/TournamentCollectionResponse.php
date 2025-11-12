<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TournamentCollectionResponse extends Response
{
    /**
     * @param TournamentResponse[] $tournaments
     */
    public function __construct(
        public readonly array $tournaments
    ) {
    }

    public function toArray(): array
    {
        return array_map(
            static fn(TournamentResponse $tournament) => $tournament->toArray(),
            $this->tournaments
        );
    }
}

