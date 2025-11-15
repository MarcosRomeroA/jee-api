<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class PostLikedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $postId,
        private readonly Uuid $likeId,
        private readonly Uuid $userLikerId,
    ) {
        parent::__construct($postId);
    }

    public static function eventName(): string
    {
        return "post.liked";
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn,
    ): DomainEvent {
        return new self(
            $aggregateId,
            new Uuid($body["likeId"]),
            new Uuid($body["userLikerId"]),
        );
    }

    public function toPrimitives(): array
    {
        return [
            "postId" => $this->getAggregateId(),
            "likeId" => $this->likeId->value(),
            "userLikerId" => $this->userLikerId->value(),
        ];
    }

    public function likeId(): Uuid
    {
        return $this->likeId;
    }

    public function userLikerId(): Uuid
    {
        return $this->userLikerId;
    }
}
