<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class GameNotFoundException extends ApiException
{
    public function __construct(string $gameId)
    {
        parent::__construct(
            "Game with id <$gameId> not found",
            'game_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}

