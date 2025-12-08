<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Video;

use App\Contexts\Web\Post\Domain\Video\VideoValidator;
use Psr\Log\LoggerInterface;
use RuntimeException;

final readonly class FFmpegVideoValidator implements VideoValidator
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function isValidMimeType(string $mimeType): bool
    {
        return in_array($mimeType, self::ALLOWED_MIME_TYPES, true);
    }

    public function getResolution(string $videoPath): array
    {
        if (!file_exists($videoPath)) {
            throw new RuntimeException("Video file not found: $videoPath");
        }

        $command = sprintf(
            'ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=s=x:p=0 %s 2>&1',
            escapeshellarg($videoPath)
        );

        $output = shell_exec($command);

        if ($output === null || trim($output) === '') {
            $this->logger->error('FFprobe failed to get video resolution', [
                'video_path' => $videoPath,
            ]);
            throw new RuntimeException("Failed to get video resolution: $videoPath");
        }

        $parts = explode('x', trim($output));

        if (count($parts) !== 2) {
            throw new RuntimeException("Invalid resolution format from ffprobe: $output");
        }

        $width = (int) $parts[0];
        $height = (int) $parts[1];

        $this->logger->debug('Video resolution retrieved', [
            'video_path' => $videoPath,
            'width' => $width,
            'height' => $height,
        ]);

        return ['width' => $width, 'height' => $height];
    }

    public function isValidResolution(string $videoPath, int $maxHeight = self::MAX_HEIGHT): bool
    {
        $resolution = $this->getResolution($videoPath);

        return $resolution['height'] <= $maxHeight;
    }

    public function getDuration(string $videoPath): float
    {
        if (!file_exists($videoPath)) {
            throw new RuntimeException("Video file not found: $videoPath");
        }

        $command = sprintf(
            'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>&1',
            escapeshellarg($videoPath)
        );

        $output = shell_exec($command);

        if ($output === null || trim($output) === '') {
            $this->logger->error('FFprobe failed to get video duration', [
                'video_path' => $videoPath,
            ]);
            throw new RuntimeException("Failed to get video duration: $videoPath");
        }

        $duration = (float) trim($output);

        $this->logger->debug('Video duration retrieved', [
            'video_path' => $videoPath,
            'duration' => $duration,
        ]);

        return $duration;
    }

    public function isValidDuration(string $videoPath, int $maxDurationSeconds = self::MAX_DURATION_SECONDS): bool
    {
        $duration = $this->getDuration($videoPath);

        return $duration <= $maxDurationSeconds;
    }

    public function isValidFileSize(int $sizeBytes, int $maxSizeBytes = self::MAX_FILE_SIZE_BYTES): bool
    {
        return $sizeBytes <= $maxSizeBytes;
    }
}
