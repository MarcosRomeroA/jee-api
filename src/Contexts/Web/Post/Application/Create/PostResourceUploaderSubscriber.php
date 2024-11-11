<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;
use App\Contexts\Web\Post\Domain\PostResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FilesystemIterator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final class PostResourceUploaderSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private readonly KernelInterface $kernel,
        private readonly FileManager $fileManager,
        private readonly PostRepository $postRepository,
        private EntityManagerInterface $entityManager

    )
    {
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
            }
            catch (Exception $e) {
                $this->logger->error(
                    'Failed to upload resource of type '.$type.' for post '.$postId.': '.$filename.' - Error: '. $e->getMessage()
                );
            }

            $this->entityManager->flush();
        }
    }

    public static function subscribedTo(): array
    {
        return [PostCreatedDomainEvent::class];
    }
}