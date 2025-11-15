<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class LikeCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $likeId,
        private readonly Uuid $userId,
        private readonly Uuid $postId,
    ) {
        parent::__construct($likeId);
    }

    public static function eventName(): string
    {
        return "like.created";
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn,
    ): DomainEvent {
        return new self(
            $aggregateId,
            new Uuid($body["userId"]),
            new Uuid($body["postId"]),
        );
    }

    public function toPrimitives(): array
    {
        return [
            "likeId" => $this->getAggregateId(),
            "userId" => $this->userId->value(),
            "postId" => $this->postId->value(),
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
