<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\SendEmailConfirmation;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\User\Domain\Events\UserCreatedDomainEvent;

final readonly class SendEmailConfirmationOnUserCreated implements DomainEventSubscriber
{
    public function __construct(
        private EmailConfirmationSender $emailConfirmationSender,
    ) {
    }

    public function __invoke(UserCreatedDomainEvent $event): void
    {
        $this->emailConfirmationSender->send($event->getAggregateId());
    }

    public static function subscribedTo(): array
    {
        return [UserCreatedDomainEvent::class];
    }
}
