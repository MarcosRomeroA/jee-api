<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Find;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Player\Domain\Exception\GameNotFoundException;

final class GameFinder
{
    public function __construct(
        private readonly GameRepository $repository
    ) {
    }

    public function find(Uuid $id): Game
    {
        $game = $this->repository->findById($id);

        if ($game === null) {
            throw new GameNotFoundException($id->value());
        }

        return $game;
    }
}

