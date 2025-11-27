<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Post\Disable;

use App\Contexts\Backoffice\Post\Application\Disable\DisablePostCommand;
use App\Contexts\Web\Post\Domain\ModerationReason;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DisablePostRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $postId,
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [ModerationReason::class, 'values'], message: 'Invalid moderation reason')]
        public string $reason,
    ) {
    }

    public static function fromHttp(Request $request, string $postId): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            postId: $postId,
            reason: $data['reason'] ?? '',
        );
    }

    public function toCommand(): DisablePostCommand
    {
        return new DisablePostCommand(
            $this->postId,
            $this->reason,
        );
    }
}
