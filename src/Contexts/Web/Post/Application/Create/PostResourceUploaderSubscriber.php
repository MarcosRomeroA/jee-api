<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FilesystemIterator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final readonly class PostResourceUploaderSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private LoggerInterface        $logger,
        private KernelInterface        $kernel,
        private FileManager            $fileManager,
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

                $md5Checksum = base64_encode(md5_file($tempFile, true));

                $this->fileManager->upload($path, "posts/$postId/$type", $filename, $md5Checksum);

                $post = $this->postRepository->findById($postId);
                $fileUuid = new Uuid(pathinfo($filename, PATHINFO_FILENAME));
                $postResource = new PostResource($fileUuid, $filename, $typeId);
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
