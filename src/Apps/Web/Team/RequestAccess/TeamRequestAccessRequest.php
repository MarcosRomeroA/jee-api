<?php declare(strict_types=1);

namespace App\Apps\Web\Team\RequestAccess;

use App\Contexts\Web\Team\Application\RequestAccess\TeamRequestAccessCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class TeamRequestAccessRequest
{
    public function __construct(
        public string $teamId,
        public string $playerId,
    ) {}

    # TODO: sessionId debe verificarse que el jugar sea del usuario logueado
    public static function fromHttp(
        string $teamId,
        string $playerId,
        string $sessionId,
    ): self {
        return new self($teamId, $sessionId);
    }

    public function toCommand(): TeamRequestAccessCommand
    {
        return new TeamRequestAccessCommand($this->teamId, $this->playerId);
    }
}
