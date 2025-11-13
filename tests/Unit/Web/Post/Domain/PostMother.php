<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\User;

final class PostMother
{
    public static function create(
        ?Uuid $id = null,
        ?BodyValue $body = null,
        ?User $user = null,
        ?Uuid $sharedPostId = null
    ): Post {
        return new Post(
            $id ?? Uuid::random(),
            $body ?? new BodyValue('This is a test post about gaming'),
            $user ?? UserMother::random(),
            $sharedPostId
        );
    }

    public static function random(): Post
    {
        return self::create();
    }

    public static function withUser(User $user): Post
    {
        return self::create(user: $user);
    }

    public static function withBody(string $bodyText): Post
    {
        return self::create(body: new BodyValue($bodyText));
    }

    public static function shared(Uuid $sharedPostId): Post
    {
        return self::create(sharedPostId: $sharedPostId);
    }
}

