<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class MaxPlayersPerUserExceededException extends ApiException
{
    private const MAX_PLAYERS = 8;

    public function __construct()
    {
        parent::__construct(
            sprintf('A user cannot have more than %d game accounts', self::MAX_PLAYERS),
            'max_players_per_user_exceeded',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
