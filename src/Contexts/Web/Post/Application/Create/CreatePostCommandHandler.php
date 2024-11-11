<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class CreatePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostCreator $creator,
        private UserRepository $userRepository,
    )
    {
    }

    public function __invoke(CreatePostCommand $command): void
    {
        $id = new Uuid($command->id);
        $body = new BodyValue($command->body);
        $user = $this->userRepository->findById(new Uuid($command->userId));
        $this->creator->__invoke($id, $body, $user, $command->resources);
    }
}