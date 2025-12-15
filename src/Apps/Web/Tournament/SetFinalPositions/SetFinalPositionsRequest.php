<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\SetFinalPositions;

use App\Contexts\Web\Tournament\Application\SetFinalPositions\SetFinalPositionsCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SetFinalPositionsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $tournamentId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $firstPlaceTeamId,
        #[Assert\Uuid]
        public ?string $secondPlaceTeamId,
        #[Assert\Uuid]
        public ?string $thirdPlaceTeamId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
    ) {
    }

    public static function fromHttp(
        Request $request,
        string $tournamentId,
        string $sessionId,
    ): self {
        $data = json_decode($request->getContent(), true);

        $secondPlace = isset($data['secondPlaceTeamId']) && $data['secondPlaceTeamId'] !== ''
            ? $data['secondPlaceTeamId']
            : null;

        $thirdPlace = isset($data['thirdPlaceTeamId']) && $data['thirdPlaceTeamId'] !== ''
            ? $data['thirdPlaceTeamId']
            : null;

        return new self(
            $tournamentId,
            $data['firstPlaceTeamId'] ?? '',
            $secondPlace,
            $thirdPlace,
            $sessionId,
        );
    }

    public function toCommand(): SetFinalPositionsCommand
    {
        return new SetFinalPositionsCommand(
            $this->tournamentId,
            $this->firstPlaceTeamId,
            $this->secondPlaceTeamId,
            $this->thirdPlaceTeamId,
            $this->userId,
        );
    }
}
