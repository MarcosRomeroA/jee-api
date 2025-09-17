<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class FollowerCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $followerId,
        Uuid $followedId
    )
    {
        $body = [
            'followerId' => $followerId->value(),
            'followedId' => $followedId->value()
        ];
        parent::__construct($followerId, $body);
    }

    public static function eventName(): string
    {
        return 'follower.created';
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
            new Uuid($body['followedId'])
        );
    }

    public function toPrimitives(): array
    {
        return [
            'followerId' => $this->getAggregateId()->value(),
            'followedId' => $this->body['followedId'],
        ];
    }
}
