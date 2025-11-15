<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class CommentCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $id,
        private readonly Uuid $userId,
        private readonly Uuid $postId
    )
    {
        parent::__construct($id);
    }

    public static function eventName(): string
    {
        return 'comment.created';
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn
    ): DomainEvent
    {
        return new self(
            $aggregateId,
            new Uuid($body['userId']),
            new Uuid($body['postId'])
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->getAggregateId(),
            'userId' => $this->userId->value(),
            'postId' => $this->postId->value(),
        ];
    }

    public function userId(): Uuid
    {
        return $this->userId;
    }

    public function postId(): Uuid
    {
        return $this->postId;
    }
}
