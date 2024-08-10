<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class TextIsLongerThanAllowedException extends ApiException
{
    public function __construct(
        int $length,
        string $message = "Text is longer than <%d>.",
        string $uniqueCode = "text_is_longer_than_allowed_exception",
        string $statusCode = Response::HTTP_BAD_REQUEST
    )
    {
        $message = sprintf($message, $length);
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}