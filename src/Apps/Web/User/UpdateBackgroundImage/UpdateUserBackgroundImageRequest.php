<?php

declare(strict_types=1);

namespace App\Apps\Web\User\UpdateBackgroundImage;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use App\Contexts\Web\User\Application\UpdateBackgroundImage\UpdateUserBackgroundImageCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateUserBackgroundImageRequest extends BaseRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $userId,
        #[Assert\NotBlank]
        public string $image,
    ) {
    }

    public static function fromHttp(Request $request, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            userId: $sessionId,
            image: $data['image'] ?? '',
        );
    }

    public function toCommand(): UpdateUserBackgroundImageCommand
    {
        return new UpdateUserBackgroundImageCommand(
            userId: $this->userId,
            image: $this->image,
        );
    }
}
