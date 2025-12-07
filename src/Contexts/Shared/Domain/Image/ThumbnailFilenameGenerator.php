<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Image;

final class ThumbnailFilenameGenerator
{
    public const string SIZE_SMALL = '64';
    public const string SIZE_MEDIUM = '128';

    public static function generate(?string $filename, string $size = self::SIZE_MEDIUM): ?string
    {
        if ($filename === null || $filename === '') {
            return null;
        }

        $pathInfo = pathinfo($filename);
        return $pathInfo['filename'] . '_' . $size . '.' . ($pathInfo['extension'] ?? 'webp');
    }
}
