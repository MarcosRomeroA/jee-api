<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class PlayerAlreadyExistsException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            'A player with this username already exists for this game',
            'player_already_exists',
            Response::HTTP_CONFLICT
        );
    }
}

