<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\UpdateBackgroundImage;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use App\Contexts\Web\Tournament\Application\UpdateBackgroundImage\UpdateTournamentBackgroundImageCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTournamentBackgroundImageRequest extends BaseRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $tournamentId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $requesterId,
        #[Assert\NotBlank]
        public string $image,
    ) {
    }

    public static function fromHttp(Request $request, string $tournamentId, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            tournamentId: $tournamentId,
            requesterId: $sessionId,
            image: $data['image'] ?? '',
        );
    }

    public function toCommand(): UpdateTournamentBackgroundImageCommand
    {
        return new UpdateTournamentBackgroundImageCommand(
            tournamentId: $this->tournamentId,
            requesterId: $this->requesterId,
            image: $this->image,
        );
    }
}
