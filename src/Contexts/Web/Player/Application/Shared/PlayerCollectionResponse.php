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
        $data = [];

        foreach ($this->players as $player) {
            $data[] = $player->toArray();
        }

        return [
            'data' => $data,
            'metadata' => [
                'total' => count($this->players),
                'count' => count($this->players),
                'limit' => 0,
                'offset' => 0
            ]
        ];
    }
}

