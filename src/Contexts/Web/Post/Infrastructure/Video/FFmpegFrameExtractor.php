<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Video;

use App\Contexts\Web\Post\Domain\Video\VideoFrameExtractor;
use App\Contexts\Web\Post\Domain\Video\VideoValidator;
use Psr\Log\LoggerInterface;
use RuntimeException;

final readonly class FFmpegFrameExtractor implements VideoFrameExtractor
{
    public function __construct(
        private VideoValidator $videoValidator,
        private LoggerInterface $logger,
    ) {
    }

    public function extractFrames(
        string $videoPath,
        string $outputDir,
        int $frameCount = 6,
        int $frameWidth = 512,
    ): array {
        if (!file_exists($videoPath)) {
            throw new RuntimeException("Video file not found: $videoPath");
        }

        if (!is_dir($outputDir) && !mkdir($outputDir, 0775, true)) {
            throw new RuntimeException("Failed to create output directory: $outputDir");
        }

        $duration = $this->videoValidator->getDuration($videoPath);

        if ($duration <= 0) {
            throw new RuntimeException("Invalid video duration: $duration");
        }

        // Calculate equidistant timestamps, avoiding the very start and end
        $timestamps = [];
        $step = $duration / ($frameCount + 1);
        for ($i = 1; $i <= $frameCount; $i++) {
            $timestamps[] = $step * $i;
        }

        $extractedFrames = [];
        $baseFilename = pathinfo($videoPath, PATHINFO_FILENAME);

        foreach ($timestamps as $index => $timestamp) {
            $outputFile = sprintf('%s/%s_frame_%d.webp', $outputDir, $baseFilename, $index);

            $command = sprintf(
                'nice -n 19 ffmpeg -y -ss %f -i %s -vframes 1 -vf "scale=%d:-1" -c:v libwebp -quality 85 %s 2>&1',
                $timestamp,
                escapeshellarg($videoPath),
                $frameWidth,
                escapeshellarg($outputFile)
            );

            $output = shell_exec($command);

            if (!file_exists($outputFile)) {
                $this->logger->error('Failed to extract frame from video', [
                    'video_path' => $videoPath,
                    'timestamp' => $timestamp,
                    'output' => $output,
                ]);
                continue;
            }

            $extractedFrames[] = $outputFile;

            $this->logger->debug('Frame extracted', [
                'video_path' => $videoPath,
                'timestamp' => $timestamp,
                'output_file' => $outputFile,
            ]);
        }

        if (empty($extractedFrames)) {
            throw new RuntimeException("Failed to extract any frames from video: $videoPath");
        }

        $this->logger->info('Frames extracted from video', [
            'video_path' => $videoPath,
            'frame_count' => count($extractedFrames),
        ]);

        return $extractedFrames;
    }
}
