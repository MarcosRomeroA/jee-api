<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Post\Domain;

use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Comment;
use App\Contexts\Web\Post\Domain\ValueObject\CommentValue;
use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase
{
    public function testItShouldCreateAComment(): void
    {
        $id = Uuid::random();
        $commentText = new CommentValue('This is a test comment');
        $user = UserMother::random();
        $post = PostMother::random();

        $comment = Comment::create($id, $commentText, $user, $post);

        $this->assertEquals($id, $comment->getId());
        $this->assertEquals($commentText->value(), $comment->getComment()->value());
        $this->assertEquals($user, $comment->getUser());
        $this->assertEquals($post, $comment->getPost());
    }

    public function testItShouldNotBeDisabledByDefault(): void
    {
        $comment = CommentMother::random();

        $this->assertFalse($comment->isDisabled());
        $this->assertNull($comment->getModerationReason());
        $this->assertNull($comment->getDisabledAt());
    }

    public function testItShouldDisableComment(): void
    {
        $comment = CommentMother::random();

        $comment->disable(ModerationReason::HARASSMENT);

        $this->assertTrue($comment->isDisabled());
        $this->assertEquals(ModerationReason::HARASSMENT, $comment->getModerationReason());
        $this->assertNotNull($comment->getDisabledAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $comment->getDisabledAt());
    }

    public function testItShouldEnableDisabledComment(): void
    {
        $comment = CommentMother::random();
        $comment->disable(ModerationReason::HATE_SPEECH);

        $comment->enable();

        $this->assertFalse($comment->isDisabled());
        $this->assertNull($comment->getModerationReason());
        $this->assertNull($comment->getDisabledAt());
    }

    public function testItShouldDisableWithDifferentReasons(): void
    {
        $reasons = [
            ModerationReason::HARASSMENT,
            ModerationReason::HATE_SPEECH,
            ModerationReason::SEXUAL_CONTENT,
            ModerationReason::VIOLENCE,
            ModerationReason::SPAM,
        ];

        foreach ($reasons as $reason) {
            $comment = CommentMother::random();
            $comment->disable($reason);

            $this->assertTrue($comment->isDisabled());
            $this->assertEquals($reason, $comment->getModerationReason());
        }
    }
}
