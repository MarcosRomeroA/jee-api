<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\AddComment;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Comment;
use App\Contexts\Web\Post\Domain\ValueObject\CommentValue;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class AddCommentPostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostCommenter $commenter,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(AddCommentPostCommand $command): void
    {
        $user = $this->userRepository->findById(new Uuid($command->userId));

        $postId = new Uuid($command->postId);
        $commentId = new Uuid($command->commentId);
        $commentText = new CommentValue($command->comment);

        $this->commenter->__invoke($postId, $commentId, $commentText, $user);
    }
}
