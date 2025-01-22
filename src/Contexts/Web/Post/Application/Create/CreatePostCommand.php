<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreatePostCommand implements Command
{
    public function __construct(
        public string $id,
        public string $body,
        public array $resources,
        public ?string $sharedPostId,
        public string $userId,
    )
    {
    }
}