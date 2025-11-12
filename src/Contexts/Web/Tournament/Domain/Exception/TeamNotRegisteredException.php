<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class TeamNotRegisteredException extends ApiException
{
    public function __construct(string $message)
    {
        parent::__construct(
            $message,
            'team_not_registered_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}

