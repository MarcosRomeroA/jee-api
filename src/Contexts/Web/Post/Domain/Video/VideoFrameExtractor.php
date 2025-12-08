<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Video;

interface VideoFrameExtractor
{
    /**
     * Extracts equidistant frames from a video file.
     *
     * @param string $videoPath Path to the video file
     * @param string $outputDir Directory to save extracted frames
     * @param int $frameCount Number of frames to extract
     * @param int $frameWidth Width of output frames in pixels
     * @return array<string> Paths to the extracted frame files
     */
    public function extractFrames(
        string $videoPath,
        string $outputDir,
        int $frameCount = 6,
        int $frameWidth = 512,
    ): array;
}
