<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class CommentNotFoundException extends ApiException
{
    public function __construct(
        string $message = "Comment Not Found",
        string $uniqueCode = "comment_not_found_exception",
        int $statusCode = Response::HTTP_NOT_FOUND
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}