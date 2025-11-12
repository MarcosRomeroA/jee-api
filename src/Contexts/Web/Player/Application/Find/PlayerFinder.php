<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Find;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;

final class PlayerFinder
{
    public function __construct(
        private readonly PlayerRepository $repository
    ) {
    }

    public function find(Uuid $id): Player
    {
        $player = $this->repository->findById($id);

        if ($player === null) {
            throw new PlayerNotFoundException($id->value());
        }

        return $player;
    }
}

