<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidEventDateException extends ApiException
{
    public function __construct(string $message)
    {
        parent::__construct(
            $message,
            'invalid_event_date_exception',
            Response::HTTP_BAD_REQUEST
        );
    }
}
