<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidEventTypeException extends ApiException
{
    public function __construct(string $type)
    {
        parent::__construct(
            "Invalid event type <$type>. Valid types are: presencial, virtual",
            'invalid_event_type_exception',
            Response::HTTP_BAD_REQUEST
        );
    }
}
