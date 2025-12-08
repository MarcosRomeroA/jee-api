<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Video;

interface VideoTranscoder
{
    /**
     * Transcodes a video to an optimized format.
     *
     * @param string $inputPath Path to the input video
     * @param string $outputPath Path for the output video
     * @param int $maxHeight Maximum height in pixels (e.g., 720 for 720p)
     * @return string Path to the transcoded video
     */
    public function transcode(
        string $inputPath,
        string $outputPath,
        int $maxHeight = 720,
    ): string;
}
