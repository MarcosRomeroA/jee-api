<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Conversation\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\Conversation;

final class ConversationMother
{
    public static function create(
        ?Uuid $id = null
    ): Conversation {
        return Conversation::create(
            $id ?? Uuid::random()
        );
    }

    public static function random(): Conversation
    {
        return self::create();
    }
}
