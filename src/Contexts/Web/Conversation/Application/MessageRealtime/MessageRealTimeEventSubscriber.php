<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\MessageRealtime;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Conversation\Domain\Events\MessageCreatedEvent;
use App\Contexts\Web\Conversation\Application\Shared\MessageResponse;

readonly class MessageRealTimeEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private HubInterface $hub,
        private MessageRepository $messageRepository,
    ) {}

    public function __invoke(MessageCreatedEvent $event): void
    {
        $message = $this->messageRepository->findByIdOrFail(
            $event->getAggregateId(),
        );

        $update = new Update(
            $_ENV["APP_URL"] .
                "/conversation/" .
                $message->getConversation()->getId()->value(),
            json_encode(
                MessageResponse::fromEntity(
                    $message,
                    $event->toPrimitives()["userId"]->value(),
                )->toArray(),
            ),
        );

        try {
            $this->hub->publish($update);
        } catch (\Exception $e) {
            // Log error but don't fail the request - Mercure is optional for tests
            error_log(
                "Failed to publish message to Mercure: " . $e->getMessage(),
            );
        }
    }

    public static function subscribedTo(): array
    {
        return [MessageCreatedEvent::class];
    }
}
