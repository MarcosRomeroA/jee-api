<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Image\ImageOptimizer;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Post\Domain\Events\PostVideoUploadedDomainEvent;
use App\Contexts\Web\Post\Domain\Exception\VideoDurationExceededException;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;
use App\Contexts\Web\Post\Domain\Video\VideoFrameExtractor;
use App\Contexts\Web\Post\Domain\Video\VideoValidator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FilesystemIterator;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final readonly class PostResourceUploaderSubscriber implements DomainEventSubscriber
{
    private const int MAX_VIDEO_DURATION_SECONDS = 60;
    private const int FRAME_COUNT = 6;
    private const int FRAME_WIDTH = 512;

    public function __construct(
        private LoggerInterface $logger,
        private KernelInterface $kernel,
        private FilesystemOperator $defaultStorage,
        private ImageOptimizer $imageOptimizer,
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager,
        private VideoValidator $videoValidator,
        private VideoFrameExtractor $videoFrameExtractor,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(PostCreatedDomainEvent $event): void
    {
        $tempFiles = [];
        $data = $event->toPrimitives();
        $resources = $data['resources'];
        $postId = $data['id'] instanceof Uuid ? $data['id']->value() : $data['id'];

        $today = (new \DateTime())->format('Ymd');
        $yesterday = (new \DateTime('yesterday'))->format('Ymd');

        $baseDirectories = [
            $this->kernel->getProjectDir() . "/var/tmp/resource/$today/$postId",
            $this->kernel->getProjectDir() . "/var/tmp/resource/$yesterday/$postId",
        ];

        $directories = [];

        foreach ($baseDirectories as $baseDirectory) {
            foreach (PostResource::getResourceTypes() as $resourceType) {
                $directories[] = $baseDirectory . "/$resourceType";
            }
        }

        $filesystem = new Filesystem();

        foreach ($directories as $directory) {
            if ($filesystem->exists($directory)) {
                $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);

                foreach ($iterator as $fileInfo) {
                    if ($fileInfo->isFile()) {
                        $filename = $fileInfo->getFilename();
                        $uuid = pathinfo($filename, PATHINFO_FILENAME);

                        if (in_array($uuid, $resources, true)) {
                            $tempFiles[] = $fileInfo->getRealPath();
                        }
                    }
                }
            }
        }

        foreach ($tempFiles as $tempFile) {
            $fileInfo = pathinfo($tempFile);
            $filename = $fileInfo['basename'];

            $pathParts = explode('/', $fileInfo['dirname']);
            $type = $pathParts[count($pathParts) - 1];

            try {
                if ($type === 'image') {
                    $this->processImage($tempFile, $postId, $filename, $filesystem);
                } elseif ($type === 'video') {
                    $this->processVideo($tempFile, $postId, $filename, $filesystem);
                }
            } catch (Exception $e) {
                $this->logger->error(
                    'Failed to upload resource of type ' . $type . ' for post ' . $postId . ': ' . $filename . ' - Error: ' . $e->getMessage()
                );
            }

            $this->entityManager->flush();
        }

        // Clean up empty directories for this post
        foreach ($baseDirectories as $baseDirectory) {
            $this->removeEmptyDirectories($baseDirectory, $filesystem);
        }
    }

    private function processImage(string $tempFile, string $postId, string $filename, Filesystem $filesystem): void
    {
        $this->logger->info('Uploading image resource for post ' . $postId . ': ' . $filename);

        // Optimize image to WebP
        $result = $this->imageOptimizer->optimize($tempFile);
        $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        $storagePath = "jee/posts/$postId/image/$webpFilename";

        $this->defaultStorage->write($storagePath, $result->imageData, [
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000',
        ]);

        $this->logger->info('Image optimized and uploaded', [
            'path' => $storagePath,
            'original_size_kb' => $result->originalSizeKb,
            'optimized_size_kb' => $result->optimizedSizeKb,
        ]);

        $post = $this->postRepository->findById($postId);
        $fileUuid = new Uuid(pathinfo($filename, PATHINFO_FILENAME));
        $postResource = new PostResource($fileUuid, $webpFilename, PostResource::RESOURCE_TYPE_IMAGE);
        $postResource->setImageUpdatedAt(new \DateTimeImmutable());
        $post->addResource($postResource);

        // Delete temp file after successful upload
        $filesystem->remove($tempFile);
    }

    private function processVideo(string $tempFile, string $postId, string $filename, Filesystem $filesystem): void
    {
        $resourceId = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $this->logger->info('Processing video resource for post ' . $postId . ': ' . $filename);

        // 1. Validate video duration (should already be validated in controller, but double-check)
        $duration = $this->videoValidator->getDuration($tempFile);
        if ($duration > self::MAX_VIDEO_DURATION_SECONDS) {
            $filesystem->remove($tempFile);
            throw new VideoDurationExceededException($duration, self::MAX_VIDEO_DURATION_SECONDS);
        }

        // 2. Upload original video to R2 (available immediately while transcoding happens)
        $originalFilename = "$resourceId.$extension";
        $storagePath = "jee/posts/$postId/video/$originalFilename";

        $this->defaultStorage->write($storagePath, file_get_contents($tempFile), [
            'CacheControl' => 'public, max-age=31536000',
        ]);

        $this->logger->info('Original video uploaded', [
            'path' => $storagePath,
            'size_mb' => round(filesize($tempFile) / 1024 / 1024, 2),
        ]);

        // 3. Extract frames for moderation
        $framesDir = sys_get_temp_dir() . "/frames_$resourceId";
        $framePaths = $this->videoFrameExtractor->extractFrames(
            $tempFile,
            $framesDir,
            self::FRAME_COUNT,
            self::FRAME_WIDTH
        );

        // 4. Upload frames to R2
        $frameFilenames = [];
        foreach ($framePaths as $index => $framePath) {
            $frameFilename = "{$resourceId}_frame_{$index}.webp";
            $frameStoragePath = "jee/posts/$postId/video/frames/$frameFilename";

            $this->defaultStorage->write($frameStoragePath, file_get_contents($framePath), [
                'ContentType' => 'image/webp',
                'CacheControl' => 'public, max-age=31536000',
            ]);

            $frameFilenames[] = $frameFilename;
            unlink($framePath);
        }

        // Clean up frames directory
        if (is_dir($framesDir)) {
            rmdir($framesDir);
        }

        $this->logger->info('Video frames uploaded', [
            'post_id' => $postId,
            'resource_id' => $resourceId,
            'frame_count' => count($frameFilenames),
        ]);

        // 5. Create PostResource in DB
        $post = $this->postRepository->findById(new Uuid($postId));
        $fileUuid = new Uuid($resourceId);
        $postResource = new PostResource($fileUuid, $originalFilename, PostResource::RESOURCE_TYPE_VIDEO);
        $postResource->setImageUpdatedAt(new \DateTimeImmutable());
        $post->addResource($postResource);

        // 6. Delete temp file
        $filesystem->remove($tempFile);

        // 7. Dispatch event for video moderation
        $this->eventBus->publish(new PostVideoUploadedDomainEvent(
            new Uuid($postId),
            $resourceId,
            $frameFilenames
        ));
    }

    /**
     * Recursively remove empty directories
     */
    private function removeEmptyDirectories(string $directory, Filesystem $filesystem): void
    {
        if (!$filesystem->exists($directory) || !is_dir($directory)) {
            return;
        }

        // First, clean up subdirectories
        $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                $this->removeEmptyDirectories($fileInfo->getRealPath(), $filesystem);
            }
        }

        // Check if directory is now empty and remove it
        $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);
        if (!$iterator->valid()) {
            $filesystem->remove($directory);
        }
    }

    public static function subscribedTo(): array
    {
        return [PostCreatedDomainEvent::class];
    }
}
