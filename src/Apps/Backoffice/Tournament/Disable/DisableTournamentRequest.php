<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Tournament\Disable;

use App\Contexts\Backoffice\Tournament\Application\Disable\DisableTournamentCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DisableTournamentRequest
{
    public function __construct(
        public string $tournamentId,
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [\App\Contexts\Shared\Domain\Moderation\ModerationReason::class, 'values'])]
        public string $reason,
    ) {
    }

    public static function fromHttp(Request $request, string $tournamentId): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            tournamentId: $tournamentId,
            reason: $data['reason'] ?? '',
        );
    }

    public function toCommand(): DisableTournamentCommand
    {
        return new DisableTournamentCommand(
            tournamentId: $this->tournamentId,
            reason: $this->reason,
        );
    }
}
