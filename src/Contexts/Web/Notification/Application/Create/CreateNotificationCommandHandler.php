<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Notification\Domain\NotificationTypeRepository;

final readonly class CreateNotificationCommandHandler implements CommandHandler
{
    public function __construct(
        private NotificationCreator $creator,
        private NotificationTypeRepository $notificationTypeRepository,
        private UserRepository $userRepository,
        private PostRepository $postRepository,
    )
    {
    }

    public function __invoke(CreateNotificationCommand $command): void
    {
        $id = new Uuid($command->id);
        $notificationTypeName = $command->notificationTypeName;
        $userId = new Uuid($command->userId);
        
        $notificationType = $this->notificationTypeRepository->findByName($notificationTypeName);

        $user = $this->userRepository->findById($userId);
        
        $post = null;
        if ($command->postId !== null) {
            $postId = new Uuid($command->postId);
            $post = $this->postRepository->findById($postId);
        }

        $this->creator->__invoke($id, $notificationType, $user, $post);
    }
}

