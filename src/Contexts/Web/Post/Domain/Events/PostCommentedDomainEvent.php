<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class PostCommentedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $postId,
        private readonly Uuid $commentId,
        private readonly Uuid $userCommenterId,
    ) {
        parent::__construct($postId);
    }

    public static function eventName(): string
    {
        return "post.commented";
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn,
    ): DomainEvent {
        return new self(
            $aggregateId,
            new Uuid($body["commentId"]),
            new Uuid($body["userCommenterId"]),
        );
    }

    public function toPrimitives(): array
    {
        return [
            "postId" => $this->getAggregateId(),
            "commentId" => $this->commentId->value(),
            "userCommenterId" => $this->userCommenterId->value(),
        ];
    }

    public function commentId(): Uuid
    {
        return $this->commentId;
    }

    public function userCommenterId(): Uuid
    {
        return $this->userCommenterId;
    }
}
