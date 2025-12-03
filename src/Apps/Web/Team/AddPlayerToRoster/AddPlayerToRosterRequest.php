<?php declare(strict_types=1);

namespace App\Apps\Web\Team\AddPlayerToRoster;

use App\Contexts\Web\Team\Application\AddPlayerToRoster\AddPlayerToRosterCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AddPlayerToRosterRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $id,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $rosterId,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $teamId,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $playerId,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $requesterId,
        #[Assert\Type('bool')]
        public bool $isStarter = false,
        #[Assert\Type('bool')]
        public bool $isLeader = false,
        #[Assert\Type('string')]
        public ?string $gameRoleId = null,
    ) {
    }

    public static function fromHttp(
        Request $request,
        string $teamId,
        string $rosterId,
        string $rosterPlayerId,
        string $sessionId,
    ): self {
        $data = json_decode($request->getContent(), true);

        return new self(
            $rosterPlayerId,
            $rosterId,
            $teamId,
            $data['playerId'] ?? '',
            $sessionId,
            $data['isStarter'] ?? false,
            $data['isLeader'] ?? false,
            $data['gameRoleId'] ?? null,
        );
    }

    public function toCommand(): AddPlayerToRosterCommand
    {
        return new AddPlayerToRosterCommand(
            $this->id,
            $this->rosterId,
            $this->teamId,
            $this->playerId,
            $this->isStarter,
            $this->isLeader,
            $this->gameRoleId,
            $this->requesterId,
        );
    }
}
