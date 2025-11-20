<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Post\Application;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Create\PostCreator;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Tests\Unit\Web\Post\Domain\PostMother;
use App\Tests\Unit\Web\Post\Domain\UserMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PostCreatorTest extends TestCase
{
    private PostRepository|MockObject $repository;
    private EventBus|MockObject $bus;
    private PostCreator $creator;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PostRepository::class);
        $this->bus = $this->createMock(EventBus::class);
        $this->creator = new PostCreator($this->repository, $this->bus);
    }

    public function testItShouldCreatePostWithHashtags(): void
    {
        // Arrange
        $postId = Uuid::random();
        $body = new BodyValue('This is a test post with #testing #php and #symfony hashtags!');
        $user = UserMother::random();
        $resources = [];
        $sharedPostId = null;

        $this->repository
            ->expects($this->once())
            ->method('checkIsPostExists')
            ->with($postId);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Post $post) use ($postId, $body, $user) {
                return $post->getId()->value() === $postId->value()
                    && $post->getBody()->value() === $body->value()
                    && $post->getUser()->getId()->value() === $user->getId()->value();
            }));

        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->isType('array'));

        // Act
        ($this->creator)($postId, $body, $user, $resources, $sharedPostId);

        // Assert - Expectations verified by mocks
        $this->assertTrue(true);
    }

    public function testItShouldCreatePostWithResources(): void
    {
        // Arrange
        $postId = Uuid::random();
        $body = new BodyValue('Post with images #gaming');
        $user = UserMother::random();
        $resources = [
            'https://example.com/image1.jpg',
            'https://example.com/image2.jpg'
        ];
        $sharedPostId = null;

        $this->repository
            ->expects($this->once())
            ->method('checkIsPostExists')
            ->with($postId);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Post::class));

        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->isType('array'));

        // Act
        ($this->creator)($postId, $body, $user, $resources, $sharedPostId);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldCreateSharedPost(): void
    {
        // Arrange
        $postId = Uuid::random();
        $sharedPostId = Uuid::random();
        $body = new BodyValue('Sharing this awesome post! #share');
        $user = UserMother::random();
        $resources = [];

        // Original post that will be shared
        $originalPost = PostMother::create(id: $sharedPostId);

        $this->repository
            ->expects($this->once())
            ->method('checkIsPostExists')
            ->with($postId);

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($sharedPostId)
            ->willReturn($originalPost);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Post $post) use ($postId, $sharedPostId) {
                return $post->getId()->value() === $postId->value()
                    && $post->getSharedPostId()?->value() === $sharedPostId->value();
            }));

        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->isType('array'));

        // Act
        ($this->creator)($postId, $body, $user, $resources, $sharedPostId);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldPublishDomainEventsAfterCreation(): void
    {
        // Arrange
        $postId = Uuid::random();
        $body = new BodyValue('Testing event publishing #events');
        $user = UserMother::random();
        $resources = [];
        $sharedPostId = null;

        $this->repository
            ->expects($this->once())
            ->method('checkIsPostExists')
            ->with($postId);

        $this->repository
            ->expects($this->once())
            ->method('save');

        // The important assertion: verify events are published
        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->callback(function (array $events) {
                return is_array($events) && !empty($events);
            }));

        // Act
        ($this->creator)($postId, $body, $user, $resources, $sharedPostId);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldCreatePostWithoutHashtags(): void
    {
        // Arrange
        $postId = Uuid::random();
        $body = new BodyValue('Simple post without any hashtags');
        $user = UserMother::random();
        $resources = [];
        $sharedPostId = null;

        $this->repository
            ->expects($this->once())
            ->method('checkIsPostExists')
            ->with($postId);

        $this->repository
            ->expects($this->once())
            ->method('save');

        $this->bus
            ->expects($this->once())
            ->method('publish');

        // Act
        ($this->creator)($postId, $body, $user, $resources, $sharedPostId);

        // Assert
        $this->assertTrue(true);
    }
}
