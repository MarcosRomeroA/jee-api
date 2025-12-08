<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidVideoMimeTypeException extends ApiException
{
    public function __construct(string $mimeType)
    {
        parent::__construct(
            sprintf('Invalid video MIME type: %s. Allowed types: mp4, webm, quicktime, avi, mpeg', $mimeType),
            'invalid_video_mime_type_exception',
            Response::HTTP_BAD_REQUEST
        );
    }
}
