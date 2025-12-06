<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Comment;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\ValueObject\CommentValue;
use App\Contexts\Web\User\Domain\User;

final class CommentMother
{
    public static function create(
        ?Uuid $id = null,
        ?CommentValue $comment = null,
        ?User $user = null,
        ?Post $post = null,
    ): Comment {
        return Comment::create(
            $id ?? Uuid::random(),
            $comment ?? new CommentValue('This is a test comment'),
            $user ?? UserMother::random(),
            $post ?? PostMother::random(),
        );
    }

    public static function random(): Comment
    {
        return self::create();
    }

    public static function withText(string $text): Comment
    {
        return self::create(comment: new CommentValue($text));
    }

    public static function withUser(User $user): Comment
    {
        return self::create(user: $user);
    }

    public static function withPost(Post $post): Comment
    {
        return self::create(post: $post);
    }
}
