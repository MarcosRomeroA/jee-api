<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class VideoDurationExceededException extends ApiException
{
    public function __construct(float $duration, int $maxDuration)
    {
        parent::__construct(
            sprintf('Video duration %.1f seconds exceeds maximum allowed %d seconds', $duration, $maxDuration),
            'video_duration_exceeded_exception',
            Response::HTTP_BAD_REQUEST
        );
    }
}
