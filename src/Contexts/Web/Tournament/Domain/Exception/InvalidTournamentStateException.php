<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidTournamentStateException extends ApiException
{
    public function __construct(string $message)
    {
        parent::__construct(
            $message,
            'invalid_tournament_state_exception',
            Response::HTTP_CONFLICT
        );
    }
}

