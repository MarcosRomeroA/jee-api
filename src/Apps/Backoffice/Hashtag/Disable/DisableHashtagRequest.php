<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Hashtag\Disable;

use App\Contexts\Backoffice\Hashtag\Application\Disable\DisableHashtagCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DisableHashtagRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $hashtagId,
    ) {
    }

    public static function fromHttp(Request $request, string $hashtagId): self
    {
        return new self(
            hashtagId: $hashtagId,
        );
    }

    public function toCommand(): DisableHashtagCommand
    {
        return new DisableHashtagCommand(
            $this->hashtagId,
        );
    }
}
