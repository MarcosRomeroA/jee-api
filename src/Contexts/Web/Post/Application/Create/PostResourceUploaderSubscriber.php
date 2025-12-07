<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FilesystemIterator;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final readonly class PostResourceUploaderSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private LoggerInterface        $logger,
        private KernelInterface        $kernel,
        private FilesystemOperator     $defaultStorage,
        private ImageOptimizer         $imageOptimizer,
        private PostRepository         $postRepository,
        private EntityManagerInterface $entityManager
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
        $postId = $data['id'];

        $today = (new \DateTime())->format('Ymd');
        $yesterday = (new \DateTime('yesterday'))->format('Ymd');

        $baseDirectories = [
            $this->kernel->getProjectDir() ."/var/tmp/resource/$today/$postId",
            $this->kernel->getProjectDir() ."/var/tmp/resource/$yesterday/$postId",
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
            $path = $fileInfo['dirname'];
            $filename = $fileInfo['basename'];

            $pathParts = explode('/', $fileInfo['dirname']);
            $type = $pathParts[count($pathParts) - 1];
            $typeId = PostResource::getResourceTypeFromName($type);

            try {
                $this->logger->info('Uploading resource of type '.$type.' for post '.$postId.': '.$filename);

                // Optimize image to WebP
                $result = $this->imageOptimizer->optimize($tempFile);
                $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $storagePath = "jee/posts/$postId/$type/$webpFilename";

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
                $postResource = new PostResource($fileUuid, $webpFilename, $typeId);
                $postResource->setImageUpdatedAt(new \DateTimeImmutable());
                $post->addResource($postResource);

                // Delete temp file after successful upload
                $filesystem->remove($tempFile);
            } catch (Exception $e) {
                $this->logger->error(
                    'Failed to upload resource of type '.$type.' for post '.$postId.': '.$filename.' - Error: '. $e->getMessage()
                );
            }

            $this->entityManager->flush();
        }

        // Clean up empty directories for this post
        foreach ($baseDirectories as $baseDirectory) {
            $this->removeEmptyDirectories($baseDirectory, $filesystem);
        }
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
