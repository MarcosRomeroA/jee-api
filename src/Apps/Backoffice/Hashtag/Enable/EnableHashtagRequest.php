<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Hashtag\Enable;

use App\Contexts\Backoffice\Hashtag\Application\Enable\EnableHashtagCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class EnableHashtagRequest
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

    public function toCommand(): EnableHashtagCommand
    {
        return new EnableHashtagCommand(
            $this->hashtagId,
        );
    }
}
