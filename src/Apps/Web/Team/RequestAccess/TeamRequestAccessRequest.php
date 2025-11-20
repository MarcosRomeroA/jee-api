<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\RequestAccess;

use App\Contexts\Web\Team\Application\RequestAccess\TeamRequestAccessCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class TeamRequestAccessRequest
{
    public function __construct(
        public string $teamId,
        public string $playerId,
    ) {
    }

    public static function fromHttp(
        Request $request,
        string $teamId,
        string $sessionId,
    ): self {
        // El playerId es el sessionId (usuario autenticado solicitando unirse)
        return new self($teamId, $sessionId);
    }

    public function toCommand(): TeamRequestAccessCommand
    {
        return new TeamRequestAccessCommand($this->teamId, $this->playerId);
    }
}
