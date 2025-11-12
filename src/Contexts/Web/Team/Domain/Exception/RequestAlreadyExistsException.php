<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class RequestAlreadyExistsException extends ApiException
{
    public function __construct(string $message)
    {
        parent::__construct(
            $message,
            'request_already_exists_exception',
            Response::HTTP_CONFLICT
        );
    }
}

