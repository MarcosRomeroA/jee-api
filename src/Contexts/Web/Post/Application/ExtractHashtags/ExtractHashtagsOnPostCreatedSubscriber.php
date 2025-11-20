<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\ExtractHashtags;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Hashtag;
use App\Contexts\Web\Post\Domain\HashtagRepository;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;

final readonly class ExtractHashtagsOnPostCreatedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostRepository $postRepository,
        private HashtagRepository $hashtagRepository
    ) {
    }

    public function __invoke(PostCreatedDomainEvent $event): void
    {
        // Try to find the post - it might not exist if processed asynchronously after deletion
        try {
            $post = $this->postRepository->findById($event->getAggregateId());
        } catch (\Exception $e) {
            // Post was deleted before async processing
            return;
        }

        // Extract hashtags from post body
        $hashtags = $this->extractHashtags($post->getBody()->value());

        // Clear existing hashtags
        $post->clearHashtags();

        // Process each hashtag
        foreach ($hashtags as $hashtagText) {
            // Find or create hashtag
            $hashtag = $this->hashtagRepository->findByTag($hashtagText);

            if (!$hashtag) {
                $hashtag = Hashtag::create(
                    Uuid::random(),
                    $hashtagText
                );
            }

            // Increment counter and update timestamp
            $hashtag->incrementCount();
            $this->hashtagRepository->save($hashtag);

            // Associate hashtag with post
            $post->addHashtag($hashtag);
        }

        // Save the post with updated hashtags
        $this->postRepository->save($post);
    }

    /**
     * Extract hashtags from text.
     * Returns array of unique normalized hashtags.
     *
     * @param string $text
     * @return array<string>
     */
    private function extractHashtags(string $text): array
    {
        // Match hashtags: # followed by alphanumeric characters
        // Supports Unicode letters and numbers
        preg_match_all('/#([a-zA-Z0-9\p{L}\p{N}]+)/u', $text, $matches);

        if (empty($matches[1])) {
            return [];
        }

        // Normalize and deduplicate
        $hashtags = array_map(
            fn ($tag) => Hashtag::normalize($tag),
            $matches[1]
        );

        // Remove empty strings and duplicates
        $hashtags = array_filter($hashtags, fn ($tag) => $tag !== '');
        $hashtags = array_unique($hashtags);

        return array_values($hashtags);
    }

    public static function subscribedTo(): array
    {
        return [PostCreatedDomainEvent::class];
    }
}
