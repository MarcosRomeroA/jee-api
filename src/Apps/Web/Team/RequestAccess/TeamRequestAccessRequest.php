<?php declare(strict_types=1);

namespace App\Apps\Web\Team\RequestAccess;

use App\Contexts\Web\Team\Application\RequestAccess\TeamRequestAccessCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class TeamRequestAccessRequest
{
    public function __construct(
        public string $teamId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $playerId,
    ) {}

    public static function fromHttp(Request $request, string $teamId): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $teamId,
            $data['playerId'] ?? ''
        );
    }

    public function toCommand(): TeamRequestAccessCommand
    {
        return new TeamRequestAccessCommand(
            $this->teamId,
            $this->playerId
        );
    }
}

