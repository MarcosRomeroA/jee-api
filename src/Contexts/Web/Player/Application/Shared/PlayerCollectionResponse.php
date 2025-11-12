<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class PlayerCollectionResponse extends Response
{
    /** @var PlayerResponse[] */
    public array $players;

    /**
     * @param array<PlayerResponse> $players
     */
    public function __construct(array $players)
    {
        $this->players = $players;
    }

    public function toArray(): array
    {
        $response = [];

        foreach ($this->players as $player) {
            $response[] = $player->toArray();
        }

        return $response;
    }
}

