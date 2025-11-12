<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class PlayerNotFoundException extends ApiException
{
    public function __construct(string $playerId)
    {
        parent::__construct(
            "Player with id <$playerId> not found",
            'player_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}

