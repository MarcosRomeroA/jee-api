<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;

final class GameRankNotFoundException extends ApiException
{
    public function __construct(string $id)
    {
        parent::__construct(
            "Game rank not found with id: $id",
            "game_rank_not_found",
            404
        );
    }
}

