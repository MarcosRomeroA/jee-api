<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank\Subscriber;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Player\Domain\Events\PlayerCreatedDomainEvent;

final readonly class VerifyPlayerRankOnPlayerCreatedSubscriber implements DomainEventSubscriber
{
    public function __construct()
    {
    }

    public function __invoke(PlayerCreatedDomainEvent $event): void
    {
        // TODO: Implement rank verification when Tracker.gg API key is approved
        // $this->playerRankVerifier->__invoke($event->getAggregateId());
    }

    public static function subscribedTo(): array
    {
        return [PlayerCreatedDomainEvent::class];
    }
}
