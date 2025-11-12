<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Delete;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\PlayerRepository;

final class PlayerDeleter
{
    public function __construct(
        private readonly PlayerRepository $repository
    ) {
    }

    public function delete(Uuid $id): void
    {
        $player = $this->repository->findById($id);

        if ($player === null) {
            throw new PlayerNotFoundException($id->value());
        }

        $this->repository->delete($player);
    }
}

