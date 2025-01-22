<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class PostDeletionNotAllowedException extends ApiException
{
    public function __construct(
        string $message = "Post Deletion Not Allowed",
        string $uniqueCode = "post_deletion_not_allowed_exception",
        int $statusCode = Response::HTTP_FORBIDDEN
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}