<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Conversation\Domain\Conversation;

final class ConversationResponse extends Response
{
    public function __construct(
        public readonly string $id,
    )
    {
    }

    public static function fromEntity(Conversation $conversation): self
    {
        return new self(
            $conversation->getId()->value(),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}