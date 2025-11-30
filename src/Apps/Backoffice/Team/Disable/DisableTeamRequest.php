<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Team\Disable;

use App\Contexts\Backoffice\Team\Application\Disable\DisableTeamCommand;
use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DisableTeamRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $teamId,
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [ModerationReason::class, 'values'], message: 'Invalid moderation reason')]
        public string $reason,
    ) {
    }

    public static function fromHttp(Request $request, string $teamId): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            teamId: $teamId,
            reason: $data['reason'] ?? '',
        );
    }

    public function toCommand(): DisableTeamCommand
    {
        return new DisableTeamCommand(
            $this->teamId,
            $this->reason,
        );
    }
}
