<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class VideoResolutionExceededException extends ApiException
{
    public function __construct(int $width, int $height, int $maxHeight)
    {
        parent::__construct(
            sprintf('Video resolution %dx%d exceeds maximum allowed %dp', $width, $height, $maxHeight),
            'video_resolution_exceeded_exception',
            Response::HTTP_BAD_REQUEST
        );
    }
}
