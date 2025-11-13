<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function testItShouldCreateAPost(): void
    {
        $id = Uuid::random();
        $body = new BodyValue('This is my first post about gaming!');
        $user = UserMother::random();

        $post = new Post($id, $body, $user, null);

        $this->assertEquals($id, $post->getId());
        $this->assertEquals($body->value(), $post->getBody()->value());
        $this->assertEquals($user, $post->getUser());
        $this->assertNull($post->getSharedPostId());
    }

    public function testItShouldCreateASharedPost(): void
    {
        $sharedPostId = Uuid::random();
        $post = PostMother::shared($sharedPostId);

        $this->assertEquals($sharedPostId, $post->getSharedPostId());
    }

    public function testItShouldHaveEmptyCommentsInitially(): void
    {
        $post = PostMother::random();

        $this->assertCount(0, $post->getComments());
    }
}

