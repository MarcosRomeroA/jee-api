<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post;

use App\Contexts\Web\Post\Domain\PostRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\Post\Application\Create\PostCreator;
use App\Contexts\Web\Post\Domain\HashtagRepository;
use Doctrine\DBAL\Connection;

class CreatePostWithHashtagsAsyncTest extends KernelTestCase
{
    private PostRepository $postRepository;
    private HashtagRepository $hashtagRepository;
    private UserRepository $userRepository;
    private PostCreator $postCreator;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->postRepository = $container->get(PostRepository::class);
        $this->hashtagRepository = $container->get(HashtagRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->postCreator = $container->get(PostCreator::class);

        // Disable database transaction isolation for this test
        $connection = $container->get(Connection::class);
        $connection->setAutoCommit(true);
    }

    public function testCreatePostWithHashtagsProcessedAsynchronously(): void
    {
        // Arrange: Get a test user
        $users = $this->userRepository->searchAll();

        if (empty($users)) {
            $this->markTestSkipped('No users found in database. Please create a user first.');
        }

        $user = $users[0];

        // Create a post with hashtags
        $postId = Uuid::random();
        $body = new BodyValue('Testing async processing with #supervisor #rabbitmq and #symfony!');

        // Act: Create the post (this will publish event to RabbitMQ)
        ($this->postCreator)(
            $postId,
            $body,
            $user,
            [],
            null
        );

        // Force flush
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $em->flush();

        echo "\n✅ Post created successfully!\n";
        echo "Post ID: " . $postId->value() . "\n";
        echo "Body: " . $body->value() . "\n";
        echo "\n⏳ Waiting 5 seconds for async worker to process the event...\n";

        // Wait for async processing
        sleep(5);

        // Assert: Check that hashtags were created and associated
        $post = $this->postRepository->findById($postId);
        $this->assertNotNull($post, 'Post should exist');

        $hashtags = $post->getHashtags();
        $this->assertCount(3, $hashtags, 'Post should have 3 hashtags');

        $hashtagNames = array_map(fn ($h) => $h->getTag(), $hashtags->toArray());
        $this->assertContains('supervisor', $hashtagNames);
        $this->assertContains('rabbitmq', $hashtagNames);
        $this->assertContains('symfony', $hashtagNames);

        echo "\n✅ Hashtags processed successfully by async worker!\n";
        echo "Found hashtags: " . implode(', ', $hashtagNames) . "\n";

        // Cleanup
        $em->remove($post);
        $em->flush();
    }
}
