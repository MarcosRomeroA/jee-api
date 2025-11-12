<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class GameRankNotFoundException extends ApiException
{
    public function __construct(string $gameRankId)
    {
        parent::__construct(
            "Game rank with id <$gameRankId> not found",
            'game_rank_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}

