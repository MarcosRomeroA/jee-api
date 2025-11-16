<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class TeamGameNotFoundException extends ApiException
{
    public function __construct(string $message = "Team game not found")
    {
        parent::__construct($message, 'team_game_not_found', Response::HTTP_NOT_FOUND);
    }
}
