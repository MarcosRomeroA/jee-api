<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Moderation;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Events\PostModeratedDomainEvent;
use App\Contexts\Web\Post\Domain\Moderation\ImageModerationService;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class PostImageModerator
{
    public function __construct(
        private PostRepository $postRepository,
        private ImageModerationService $imageModerationService,
        private string $cdnBaseUrl,
        private EntityManagerInterface $entityManager,
        private EventBus $eventBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Uuid $postId): void
    {
        try {
            $post = $this->postRepository->findById($postId);
        } catch (\Exception $e) {
            $this->logger->warning('Post not found for image moderation', [
                'post_id' => $postId->value(),
            ]);
            return;
        }

        if ($post->isDisabled()) {
            return;
        }

        $resources = $post->getResources();

        if ($resources === null || $resources->isEmpty()) {
            return;
        }

        foreach ($resources as $resource) {
            if ($resource->getResourceType() !== PostResource::RESOURCE_TYPE_IMAGE) {
                continue;
            }

            $imageUrl = $resource->getImageUrl($this->cdnBaseUrl, $post->getId()->value());

            $moderationReason = $this->imageModerationService->moderate($imageUrl);

            if ($moderationReason !== null) {
                $post->disable($moderationReason);
                $this->entityManager->flush();

                $this->eventBus->publish([new PostModeratedDomainEvent($postId, $moderationReason->value)]);

                $this->logger->info('Post disabled due to image moderation', [
                    'post_id' => $postId->value(),
                    'resource_id' => $resource->getId()->value(),
                    'reason' => $moderationReason->value,
                ]);

                return;
            }
        }
    }
}
