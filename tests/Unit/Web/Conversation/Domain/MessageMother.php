<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Conversation\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Web\Conversation\Domain\ValueObject\ContentValue;
use App\Contexts\Web\User\Domain\User;

final class MessageMother
{
    public static function create(
        ?Uuid $id = null,
        ?Conversation $conversation = null,
        ?User $user = null,
        ?ContentValue $content = null
    ): Message {
        return Message::create(
            $id ?? Uuid::random(),
            $conversation ?? ConversationMother::random(),
            $user ?? UserMother::random(),
            $content ?? new ContentValue('Test message content')
        );
    }

    public static function random(): Message
    {
        return self::create();
    }

    public static function withContent(string $content): Message
    {
        return self::create(content: new ContentValue($content));
    }
}
