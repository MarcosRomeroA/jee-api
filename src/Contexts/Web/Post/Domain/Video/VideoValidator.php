<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Video;

interface VideoValidator
{
    public const array ALLOWED_MIME_TYPES = [
        'video/mp4',
        'video/webm',
        'video/quicktime',
        'video/x-msvideo',
        'video/mpeg',
        'video/x-matroska',
    ];

    public const int MAX_FILE_SIZE_BYTES = 50 * 1024 * 1024; // 50MB
    public const int MAX_DURATION_SECONDS = 60;
    public const int MAX_HEIGHT = 1080;

    /**
     * Validates the MIME type of a video file.
     *
     * @param string $mimeType The MIME type to validate
     * @return bool True if valid
     */
    public function isValidMimeType(string $mimeType): bool;

    /**
     * Gets the resolution of a video.
     *
     * @param string $videoPath Path to the video file
     * @return array{width: int, height: int}
     */
    public function getResolution(string $videoPath): array;

    /**
     * Validates that video resolution does not exceed the maximum.
     *
     * @param string $videoPath Path to the video file
     * @param int $maxHeight Maximum allowed height in pixels
     * @return bool True if valid
     */
    public function isValidResolution(string $videoPath, int $maxHeight = self::MAX_HEIGHT): bool;

    /**
     * Gets the duration of a video in seconds.
     *
     * @param string $videoPath Path to the video file
     * @return float Duration in seconds
     */
    public function getDuration(string $videoPath): float;

    /**
     * Validates that video duration does not exceed the maximum.
     *
     * @param string $videoPath Path to the video file
     * @param int $maxDurationSeconds Maximum allowed duration in seconds
     * @return bool True if valid
     */
    public function isValidDuration(string $videoPath, int $maxDurationSeconds = self::MAX_DURATION_SECONDS): bool;

    /**
     * Validates file size.
     *
     * @param int $sizeBytes File size in bytes
     * @param int $maxSizeBytes Maximum allowed size in bytes
     * @return bool True if valid
     */
    public function isValidFileSize(int $sizeBytes, int $maxSizeBytes = self::MAX_FILE_SIZE_BYTES): bool;
}
