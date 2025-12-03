<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Conversation\Domain\Message;

final class MessageResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $content,
        public readonly string $username,
        public readonly bool $mine,
        public readonly string $createdAt,
        public readonly ?string $readAt,
    )
    {
    }

    public static function fromEntity(Message $message, string $sessionUserId): self
    {
        $readAtValue = $message->getReadAt()->value();

        return new self(
            $message->getId()->value(),
            $message->getContent()->value(),
            $message->getUser()->getUsername()->value(),
            $message->getUser()->getId()->value() === $sessionUserId,
            $message->getCreatedAt()->value()->format('c'),
            $readAtValue?->format('c'),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
