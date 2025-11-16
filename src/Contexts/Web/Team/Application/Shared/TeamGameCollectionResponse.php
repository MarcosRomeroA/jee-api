<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TeamGameCollectionResponse extends Response
{
    /**
     * @param TeamGameResponse[] $teamGames
     */
    public function __construct(
        public readonly array $teamGames
    ) {
    }

    /**
     * @param \App\Contexts\Web\Team\Domain\TeamGame[] $teamGames
     */
    public static function fromTeamGames(array $teamGames): self
    {
        $responses = array_map(
            fn($teamGame) => TeamGameResponse::fromTeamGame($teamGame),
            $teamGames
        );

        return new self($responses);
    }

    public function toArray(): array
    {
        return array_map(
            fn(TeamGameResponse $response) => $response->toArray(),
            $this->teamGames
        );
    }
}
