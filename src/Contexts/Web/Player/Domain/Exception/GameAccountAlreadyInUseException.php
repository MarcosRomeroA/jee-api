<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class GameAccountAlreadyInUseException extends ApiException
{
    public function __construct(string $accountIdentifier)
    {
        parent::__construct(
            "You already have a player with the game account <$accountIdentifier>",
            'game_account_already_in_use',
            Response::HTTP_CONFLICT
        );
    }
}
