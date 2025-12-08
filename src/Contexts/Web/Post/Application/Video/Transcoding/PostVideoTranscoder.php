<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Video\Transcoding;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;
use App\Contexts\Web\Post\Domain\Video\VideoTranscoder;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;

final readonly class PostVideoTranscoder
{
    private const int MAX_HEIGHT = 720;

    public function __construct(
        private PostRepository $postRepository,
        private VideoTranscoder $videoTranscoder,
        private FilesystemOperator $defaultStorage,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Uuid $postId, string $resourceId, string $originalFilename): void
    {
        $post = $this->postRepository->findById($postId);

        if ($post === null) {
            $this->logger->warning('Post not found for video transcoding', [
                'post_id' => $postId->value(),
            ]);
            return;
        }

        // Find the resource first
        $resource = $this->findResourceById($post, $resourceId);
        if ($resource === null) {
            $this->logger->warning('PostResource not found for video transcoding', [
                'post_id' => $postId->value(),
                'resource_id' => $resourceId,
            ]);
            return;
        }

        // Skip if already transcoded (prevents duplicate processing)
        if ($resource->isVideoTranscoded()) {
            $this->logger->info('Video already transcoded, skipping', [
                'post_id' => $postId->value(),
                'resource_id' => $resourceId,
            ]);
            return;
        }

        if ($post->isDisabled()) {
            $this->logger->info('Skipping transcoding for disabled post', [
                'post_id' => $postId->value(),
            ]);
            return;
        }

        $storagePath = "jee/posts/{$postId->value()}/video";

        $this->logger->info('Starting video transcoding', [
            'post_id' => $postId->value(),
            'resource_id' => $resourceId,
            'original_filename' => $originalFilename,
        ]);

        // 1. Download original video from R2
        $videoContent = $this->defaultStorage->read("$storagePath/$originalFilename");
        $originalSizeBytes = strlen($videoContent);

        $tempDir = sys_get_temp_dir();
        $inputPath = "$tempDir/$originalFilename";
        file_put_contents($inputPath, $videoContent);

        // 2. Transcode to 720p MP4
        $outputFilename = "$resourceId.mp4";
        $outputPath = "$tempDir/$outputFilename";

        try {
            $this->videoTranscoder->transcode($inputPath, $outputPath, self::MAX_HEIGHT);
        } catch (\Throwable $e) {
            // Clean up temp files on failure
            @unlink($inputPath);
            @unlink($outputPath);

            $this->logger->error('Video transcoding failed', [
                'post_id' => $postId->value(),
                'resource_id' => $resourceId,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger retry in messenger
        }

        // 3. Get transcoded file size
        $transcodedContent = file_get_contents($outputPath);
        $transcodedSizeBytes = strlen($transcodedContent);

        // 4. Upload transcoded video to R2
        $this->defaultStorage->write(
            "$storagePath/$outputFilename",
            $transcodedContent,
            [
                'ContentType' => 'video/mp4',
                'CacheControl' => 'public, max-age=31536000',
            ]
        );

        // 5. Delete original video from R2 (if different from output)
        if ($originalFilename !== $outputFilename) {
            try {
                $this->defaultStorage->delete("$storagePath/$originalFilename");
            } catch (\Throwable $e) {
                $this->logger->warning('Failed to delete original video', [
                    'path' => "$storagePath/$originalFilename",
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 6. Update PostResource
        $resource->setFilename($outputFilename);
        $resource->setImageUpdatedAt(new \DateTimeImmutable());
        $resource->markAsTranscoded();
        $this->entityManager->flush();

        // 7. Clean up temp files
        @unlink($inputPath);
        @unlink($outputPath);

        // 8. Log size reduction
        $originalSizeMb = round($originalSizeBytes / 1024 / 1024, 2);
        $transcodedSizeMb = round($transcodedSizeBytes / 1024 / 1024, 2);
        $reductionPercent = $originalSizeBytes > 0
            ? round((1 - $transcodedSizeBytes / $originalSizeBytes) * 100, 1)
            : 0;

        $this->logger->info('Video transcoding completed', [
            'post_id' => $postId->value(),
            'resource_id' => $resourceId,
            'output_filename' => $outputFilename,
            'original_size_mb' => $originalSizeMb,
            'transcoded_size_mb' => $transcodedSizeMb,
            'reduction_percent' => $reductionPercent,
        ]);
    }

    private function findResourceById($post, string $resourceId): ?PostResource
    {
        foreach ($post->getResources() as $resource) {
            if ($resource->getId()->value() === $resourceId) {
                return $resource;
            }
        }

        return null;
    }
}
