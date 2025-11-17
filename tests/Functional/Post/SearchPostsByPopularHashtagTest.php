<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Create\PostCreator;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SearchPostsByPopularHashtagTest extends KernelTestCase
{
    private PostRepository $postRepository;
    private UserRepository $userRepository;
    private PostCreator $postCreator;
    private array $createdPostIds = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->postRepository = $container->get(PostRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->postCreator = $container->get(PostCreator::class);
    }

    protected function tearDown(): void
    {
        // Clean up created posts
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        foreach ($this->createdPostIds as $postId) {
            try {
                $post = $this->postRepository->findById($postId);
                if ($post) {
                    $em->remove($post);
                }
            } catch (\Exception $e) {
                // Post already deleted or not found
            }
        }
        $em->flush();

        parent::tearDown();
    }

    public function testSearchPostsByPopularHashtag(): void
    {
        // Arrange: Get a test user
        $users = $this->userRepository->searchAll();

        if (empty($users)) {
            $this->markTestSkipped('No users found in database. Please create a user first.');
        }

        $user = $users[0];

        // Create posts with the hashtag #testing
        $postId1 = Uuid::random();
        $postId2 = Uuid::random();
        $this->createdPostIds[] = $postId1;
        $this->createdPostIds[] = $postId2;

        $body1 = new BodyValue('First post with #testing hashtag');
        $body2 = new BodyValue('Second post with #testing and #php');

        ($this->postCreator)(
            $postId1,
            $body1,
            $user,
            [],
            null
        );

        ($this->postCreator)(
            $postId2,
            $body2,
            $user,
            [],
            null
        );

        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $em->flush();

        // Act: Search posts by popular hashtag
        $posts = $this->postRepository->findByPopularHashtag('testing', 30, 10, 0);
        $total = $this->postRepository->countByPopularHashtag('testing', 30);

        // Assert
        $this->assertGreaterThanOrEqual(2, count($posts), 'Should find at least 2 posts with #testing');
        $this->assertGreaterThanOrEqual(2, $total, 'Total count should be at least 2');

        echo "\n✅ Found " . count($posts) . " posts with #testing hashtag\n";
        echo "Total count: " . $total . "\n";

        // Verify posts contain the ones we created
        $postIds = array_map(fn ($post) => $post->getId()->value(), $posts);
        $this->assertContains($postId1->value(), $postIds, 'Should contain first created post');
        $this->assertContains($postId2->value(), $postIds, 'Should contain second created post');

        echo "✅ Posts are correctly returned and contain created posts\n";
    }
}
