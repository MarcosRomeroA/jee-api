<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Video;

use App\Contexts\Web\Post\Domain\Video\VideoTranscoder;
use Psr\Log\LoggerInterface;
use RuntimeException;

final readonly class FFmpegVideoTranscoder implements VideoTranscoder
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function transcode(
        string $inputPath,
        string $outputPath,
        int $maxHeight = 720,
    ): string {
        if (!file_exists($inputPath)) {
            throw new RuntimeException("Input video file not found: $inputPath");
        }

        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir) && !mkdir($outputDir, 0775, true)) {
            throw new RuntimeException("Failed to create output directory: $outputDir");
        }

        // H.264 encoding with nice for low CPU priority
        // -preset fast: good balance between speed and compression
        // -crf 28: good quality with smaller file size
        // -movflags +faststart: enables progressive download/streaming
        $command = sprintf(
            'nice -n 19 ffmpeg -y -i %s -c:v libx264 -preset fast -crf 28 -vf "scale=-2:%d" -c:a aac -b:a 128k -movflags +faststart %s 2>&1',
            escapeshellarg($inputPath),
            $maxHeight,
            escapeshellarg($outputPath)
        );

        $this->logger->info('Starting video transcoding', [
            'input' => $inputPath,
            'output' => $outputPath,
            'max_height' => $maxHeight,
        ]);

        $startTime = microtime(true);
        $output = shell_exec($command);
        $elapsed = microtime(true) - $startTime;

        if (!file_exists($outputPath)) {
            $this->logger->error('Video transcoding failed', [
                'input' => $inputPath,
                'output' => $outputPath,
                'ffmpeg_output' => $output,
            ]);
            throw new RuntimeException("Failed to transcode video: $inputPath");
        }

        $inputSize = filesize($inputPath);
        $outputSize = filesize($outputPath);

        $this->logger->info('Video transcoding completed', [
            'input' => $inputPath,
            'output' => $outputPath,
            'input_size_mb' => round($inputSize / 1024 / 1024, 2),
            'output_size_mb' => round($outputSize / 1024 / 1024, 2),
            'compression_ratio' => $inputSize > 0 ? round($outputSize / $inputSize * 100, 1) . '%' : 'N/A',
            'elapsed_seconds' => round($elapsed, 2),
        ]);

        return $outputPath;
    }
}
