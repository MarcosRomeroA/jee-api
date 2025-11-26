<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\AddPostTempResource;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;

final readonly class AddPostTempResourceCommandHandler implements CommandHandler
{
    public function __construct(
        private PostTempResourceAdder $postTempResourceAdder
    ) {
    }

    public function __invoke(AddPostTempResourceCommand $command): void
    {
        $this->postTempResourceAdder->add(
            $command->resourceId,
            $command->postId,
            $command->type,
            $command->file,
            $command->projectDir
        );
    }
}
