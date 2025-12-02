<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\UpdateBackgroundImage;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use App\Contexts\Web\Team\Application\UpdateBackgroundImage\UpdateTeamBackgroundImageCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTeamBackgroundImageRequest extends BaseRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $teamId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $requesterId,
        #[Assert\NotBlank]
        public string $image,
    ) {
    }

    public static function fromHttp(Request $request, string $teamId, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            teamId: $teamId,
            requesterId: $sessionId,
            image: $data['image'] ?? '',
        );
    }

    public function toCommand(): UpdateTeamBackgroundImageCommand
    {
        return new UpdateTeamBackgroundImageCommand(
            teamId: $this->teamId,
            requesterId: $this->requesterId,
            image: $this->image,
        );
    }
}
