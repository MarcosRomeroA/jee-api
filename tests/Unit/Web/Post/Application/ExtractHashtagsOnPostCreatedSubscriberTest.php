<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Post\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\ExtractHashtags\ExtractHashtagsOnPostCreatedSubscriber;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Post\Domain\Hashtag;
use App\Contexts\Web\Post\Domain\HashtagRepository;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Tests\Unit\Web\Post\Domain\PostMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ExtractHashtagsOnPostCreatedSubscriberTest extends TestCase
{
    private PostRepository|MockObject $postRepository;
    private HashtagRepository|MockObject $hashtagRepository;
    private ExtractHashtagsOnPostCreatedSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->hashtagRepository = $this->createMock(HashtagRepository::class);
        $this->subscriber = new ExtractHashtagsOnPostCreatedSubscriber(
            $this->postRepository,
            $this->hashtagRepository
        );
    }

    public function testItShouldSubscribeToPostCreatedEvent(): void
    {
        // Assert
        $subscribedEvents = ExtractHashtagsOnPostCreatedSubscriber::subscribedTo();

        $this->assertContains(PostCreatedDomainEvent::class, $subscribedEvents);
    }

    public function testItShouldExtractHashtagsFromPostBody(): void
    {
        // Arrange
        $postId = Uuid::random();
        $post = PostMother::withBody('Testing async processing with #supervisor #rabbitmq and #symfony!');
        $event = new PostCreatedDomainEvent($postId, []);

        // Mock post retrieval
        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with($postId)
            ->willReturn($post);

        // Mock hashtag finding (none exist yet)
        $this->hashtagRepository
            ->expects($this->exactly(3))
            ->method('findByTag')
            ->willReturnCallback(function ($tag) {
                return null; // Hashtags don't exist yet
            });

        // Mock hashtag saving (3 new hashtags)
        $this->hashtagRepository
            ->expects($this->exactly(3))
            ->method('save')
            ->with($this->callback(function ($hashtag) {
                return $hashtag instanceof Hashtag
                    && in_array($hashtag->getTag(), ['supervisor', 'rabbitmq', 'symfony']);
            }));

        // Mock post saving
        $this->postRepository
            ->expects($this->once())
            ->method('save')
            ->with($post);

        // Act
        ($this->subscriber)($event);

        // Assert - Expectations verified by mocks
        $this->assertTrue(true);
    }

    public function testItShouldReuseExistingHashtags(): void
    {
        // Arrange
        $postId = Uuid::random();
        $post = PostMother::withBody('Post with #php and #symfony');
        $event = new PostCreatedDomainEvent($postId, []);

        // Create existing hashtags
        $existingPhpHashtag = Hashtag::create(Uuid::random(), 'php');
        $existingSymfonyHashtag = Hashtag::create(Uuid::random(), 'symfony');

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with($postId)
            ->willReturn($post);

        // Mock finding existing hashtags
        $this->hashtagRepository
            ->expects($this->exactly(2))
            ->method('findByTag')
            ->willReturnCallback(function ($tag) use ($existingPhpHashtag, $existingSymfonyHashtag) {
                return match($tag) {
                    'php' => $existingPhpHashtag,
                    'symfony' => $existingSymfonyHashtag,
                    default => null,
                };
            });

        // Both existing hashtags should have their count incremented
        $this->hashtagRepository
            ->expects($this->exactly(2))
            ->method('save')
            ->with($this->callback(function ($hashtag) {
                return $hashtag instanceof Hashtag;
            }));

        $this->postRepository
            ->expects($this->once())
            ->method('save')
            ->with($post);

        // Act
        ($this->subscriber)($event);

        // Assert - Verify counts were incremented
        $this->assertEquals(1, $existingPhpHashtag->getCount());
        $this->assertEquals(1, $existingSymfonyHashtag->getCount());
    }

    public function testItShouldHandlePostWithoutHashtags(): void
    {
        // Arrange
        $postId = Uuid::random();
        $post = PostMother::withBody('Simple post without any hashtags');
        $event = new PostCreatedDomainEvent($postId, []);

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with($postId)
            ->willReturn($post);

        // No hashtags should be searched
        $this->hashtagRepository
            ->expects($this->never())
            ->method('findByTag');

        // No hashtags should be saved
        $this->hashtagRepository
            ->expects($this->never())
            ->method('save');

        // Post should still be saved (with cleared hashtags)
        $this->postRepository
            ->expects($this->once())
            ->method('save')
            ->with($post);

        // Act
        ($this->subscriber)($event);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldHandleNonExistentPost(): void
    {
        // Arrange
        $postId = Uuid::random();
        $event = new PostCreatedDomainEvent($postId, []);

        // Post was deleted before async processing
        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with($postId)
            ->willThrowException(new \Exception('Post not found'));

        // Should not attempt any hashtag processing
        $this->hashtagRepository
            ->expects($this->never())
            ->method('findByTag');

        $this->hashtagRepository
            ->expects($this->never())
            ->method('save');

        $this->postRepository
            ->expects($this->never())
            ->method('save');

        // Act
        ($this->subscriber)($event);

        // Assert - Should handle gracefully
        $this->assertTrue(true);
    }

    public function testItShouldNormalizeHashtagsBeforeSaving(): void
    {
        // Arrange
        $postId = Uuid::random();
        $post = PostMother::withBody('Testing #PHP #Php #php all should be normalized');
        $event = new PostCreatedDomainEvent($postId, []);

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with($postId)
            ->willReturn($post);

        // Only one normalized hashtag 'php' should be searched
        $this->hashtagRepository
            ->expects($this->once()) // Only once because duplicates are removed
            ->method('findByTag')
            ->with('php')
            ->willReturn(null);

        // Only one hashtag should be saved
        $this->hashtagRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($hashtag) {
                return $hashtag instanceof Hashtag && $hashtag->getTag() === 'php';
            }));

        $this->postRepository
            ->expects($this->once())
            ->method('save')
            ->with($post);

        // Act
        ($this->subscriber)($event);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldHandleMixOfNewAndExistingHashtags(): void
    {
        // Arrange
        $postId = Uuid::random();
        $post = PostMother::withBody('Mix of #existing #new hashtags');
        $event = new PostCreatedDomainEvent($postId, []);

        $existingHashtag = Hashtag::create(Uuid::random(), 'existing');

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with($postId)
            ->willReturn($post);

        $this->hashtagRepository
            ->expects($this->exactly(2))
            ->method('findByTag')
            ->willReturnCallback(function ($tag) use ($existingHashtag) {
                return $tag === 'existing' ? $existingHashtag : null;
            });

        // Both hashtags should be saved (existing incremented, new created)
        $this->hashtagRepository
            ->expects($this->exactly(2))
            ->method('save');

        $this->postRepository
            ->expects($this->once())
            ->method('save')
            ->with($post);

        // Act
        ($this->subscriber)($event);

        // Assert
        $this->assertEquals(1, $existingHashtag->getCount());
    }
}
