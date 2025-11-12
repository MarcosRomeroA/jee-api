<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TeamCollectionResponse extends Response
{
    /**
     * @param TeamResponse[] $teams
     */
    public function __construct(
        public readonly array $teams
    ) {
    }

    public function toArray(): array
    {
        return array_map(
            static fn(TeamResponse $team) => $team->toArray(),
            $this->teams
        );
    }
}

