<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class UnauthorizedException extends ApiException
{
    public function __construct(string $message)
    {
        parent::__construct(
            $message,
            'unauthorized_exception',
            Response::HTTP_FORBIDDEN
        );
    }
}

