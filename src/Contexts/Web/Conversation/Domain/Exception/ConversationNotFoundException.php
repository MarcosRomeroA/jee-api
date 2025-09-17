<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class ConversationNotFoundException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            'Conversation Not Found',
            'conversation_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}
