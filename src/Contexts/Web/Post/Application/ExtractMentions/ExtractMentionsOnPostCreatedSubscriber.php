<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\ExtractMentions;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Notification\Domain\Notification;
use App\Contexts\Web\Notification\Domain\NotificationRepository;
use App\Contexts\Web\Notification\Domain\NotificationType;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Post\Domain\PostMention;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final readonly class ExtractMentionsOnPostCreatedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostRepository $postRepository,
        private UserRepository $userRepository,
        private NotificationRepository $notificationRepository,
        private NotificationTypeRepository $notificationTypeRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(PostCreatedDomainEvent $event): void
    {
        try {
            $post = $this->postRepository->findById($event->getAggregateId());
        } catch (\Exception $e) {
            return;
        }

        $usernames = $this->extractMentions($post->getBody()->value());

        if (empty($usernames)) {
            return;
        }

        $post->clearMentions();

        $notificationType = $this->notificationTypeRepository->findByName(
            NotificationType::USER_MENTIONED,
        );

        $postAuthor = $post->getUser();

        foreach ($usernames as $username) {
            try {
                $mentionedUser = $this->userRepository->findByUsername(new UsernameValue($username));
            } catch (\Exception $e) {
                continue;
            }

            // Don't mention yourself
            if ($mentionedUser->getId()->value() === $postAuthor->getId()->value()) {
                continue;
            }

            $mention = PostMention::create(
                Uuid::random(),
                $post,
                $mentionedUser,
            );

            $post->addMention($mention);

            $notification = Notification::create(
                Uuid::random(),
                $notificationType,
                $mentionedUser,
                $postAuthor,
                $post,
            );

            $this->notificationRepository->save($notification);

            $this->bus->publish($notification->pullDomainEvents());
        }

        $this->postRepository->save($post);
    }

    /**
     * Extract usernames from text (mentions starting with @).
     * Returns array of unique usernames without the @ symbol.
     *
     * @param string $text
     * @return array<string>
     */
    private function extractMentions(string $text): array
    {
        // Match mentions: @ followed by alphanumeric characters, underscores, dots, and hyphens
        // Note: trim trailing special chars to avoid matching "user." when user meant "@user."
        preg_match_all('/@([a-zA-Z0-9_.\-]+)/', $text, $matches);

        if (empty($matches[1])) {
            return [];
        }

        // Trim trailing dots and hyphens from usernames (e.g., "@user." should match "user")
        $usernames = array_map(
            fn (string $username) => rtrim($username, '.-'),
            $matches[1],
        );

        // Filter empty usernames and remove duplicates
        $usernames = array_filter($usernames, fn (string $username) => $username !== '');
        $usernames = array_unique($usernames);

        return array_values($usernames);
    }

    public static function subscribedTo(): array
    {
        return [PostCreatedDomainEvent::class];
    }
}
