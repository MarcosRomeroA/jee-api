<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\CQRS\Event\Messenger;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * EventBus implementation that uses Symfony Messenger to dispatch events.
 * This allows events to be processed asynchronously via RabbitMQ or other transports.
 */
final readonly class MessengerEventBus implements EventBus
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function publish(DomainEvent|array $events): void
    {
        $eventList = is_array($events) ? $events : [$events];

        foreach ($eventList as $event) {
            try {
                $this->messageBus->dispatch($event);
            } catch (NoHandlerForMessageException) {
                // Silently ignore events without handlers
            }
        }
    }
}
