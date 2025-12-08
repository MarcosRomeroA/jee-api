<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Video\Moderation;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Events\PostModeratedDomainEvent;
use App\Contexts\Web\Post\Domain\Events\PostVideoModerationPassedDomainEvent;
use App\Contexts\Web\Post\Domain\Moderation\ImageModerationService;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;

final readonly class PostVideoModerator
{
    public function __construct(
        private PostRepository $postRepository,
        private ImageModerationService $imageModerationService,
        private string $cdnBaseUrl,
        private EntityManagerInterface $entityManager,
        private EventBus $eventBus,
        private LoggerInterface $logger,
        private FilesystemOperator $defaultStorage,
    ) {
    }

    public function __invoke(Uuid $postId, string $resourceId, array $frameFilenames): void
    {
        try {
            $post = $this->postRepository->findById($postId);
        } catch (\Exception $e) {
            $this->logger->warning('Post not found for video moderation', [
                'post_id' => $postId->value(),
            ]);
            return;
        }

        // Skip if post is already disabled
        if ($post->isDisabled()) {
            $this->logger->info('Post already disabled, skipping video moderation', [
                'post_id' => $postId->value(),
                'resource_id' => $resourceId,
            ]);
            $this->deleteFrames($postId->value(), $resourceId, $frameFilenames);
            return;
        }

        // Find the PostResource
        $resource = $this->findResource($post, $resourceId);
        if ($resource === null) {
            $this->logger->warning('PostResource not found for video moderation', [
                'post_id' => $postId->value(),
                'resource_id' => $resourceId,
            ]);
            return;
        }

        // Skip if already moderated (prevents duplicate processing)
        if ($resource->isVideoModerated()) {
            $this->logger->info('Video already moderated, skipping', [
                'post_id' => $postId->value(),
                'resource_id' => $resourceId,
            ]);
            $this->deleteFrames($postId->value(), $resourceId, $frameFilenames);
            return;
        }

        $this->logger->info('Starting video moderation', [
            'post_id' => $postId->value(),
            'resource_id' => $resourceId,
            'frame_count' => count($frameFilenames),
        ]);

        // Moderate each frame - stop at first failure
        foreach ($frameFilenames as $index => $frameFilename) {
            $frameUrl = rtrim($this->cdnBaseUrl, '/') . "/jee/posts/{$postId->value()}/video/frames/$frameFilename";

            $this->logger->debug('Moderating video frame', [
                'post_id' => $postId->value(),
                'resource_id' => $resourceId,
                'frame_index' => $index,
                'frame_url' => $frameUrl,
            ]);

            $moderationReason = $this->imageModerationService->moderate($frameUrl);

            $this->logger->debug('Video frame moderation result', [
                'post_id' => $postId->value(),
                'resource_id' => $resourceId,
                'frame_index' => $index,
                'result' => $moderationReason?->value ?? 'passed',
            ]);

            if ($moderationReason !== null) {
                $post->disable($moderationReason);
                $this->entityManager->flush();

                $this->eventBus->publish([new PostModeratedDomainEvent($postId, $moderationReason->value)]);

                $this->logger->info('Post disabled due to video frame moderation', [
                    'post_id' => $postId->value(),
                    'resource_id' => $resourceId,
                    'frame' => $frameFilename,
                    'reason' => $moderationReason->value,
                ]);

                $this->deleteFrames($postId->value(), $resourceId, $frameFilenames);
                return;
            }
        }

        // Mark as moderated to prevent duplicate processing
        $resource->markAsModerated();
        $this->entityManager->flush();

        $this->logger->info('Video moderation passed', [
            'post_id' => $postId->value(),
            'resource_id' => $resourceId,
        ]);

        $this->deleteFrames($postId->value(), $resourceId, $frameFilenames);

        // Dispatch event for transcoding
        $this->eventBus->publish(new PostVideoModerationPassedDomainEvent(
            $postId,
            $resourceId,
            $resource->getFilename()
        ));
    }

    private function findResource($post, string $resourceId): ?PostResource
    {
        foreach ($post->getResources() as $resource) {
            if ($resource->getId()->value() === $resourceId) {
                return $resource;
            }
        }
        return null;
    }

    private function deleteFrames(string $postId, string $resourceId, array $frameFilenames): void
    {
        foreach ($frameFilenames as $frameFilename) {
            $framePath = "jee/posts/$postId/video/frames/$frameFilename";
            try {
                $this->defaultStorage->delete($framePath);
                $this->logger->debug('Deleted moderation frame', ['path' => $framePath]);
            } catch (\Throwable $e) {
                $this->logger->warning('Failed to delete moderation frame', [
                    'path' => $framePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

}
