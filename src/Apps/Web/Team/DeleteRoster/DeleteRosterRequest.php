<?php declare(strict_types=1);

namespace App\Apps\Web\Team\DeleteRoster;

use App\Contexts\Web\Team\Application\DeleteRoster\DeleteRosterCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteRosterRequest
{
    public function __construct(
        #[Assert\NotBlank] #[Assert\Type("string")] public string $rosterId,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $teamId,
        public string $requesterId = '',
    ) {
    }

    public static function fromHttp(
        Request $request,
        string $teamId,
        string $rosterId,
        string $sessionId,
    ): self {
        return new self(
            $rosterId,
            $teamId,
            $sessionId,
        );
    }

    public function toCommand(): DeleteRosterCommand
    {
        return new DeleteRosterCommand(
            $this->rosterId,
            $this->teamId,
            $this->requesterId,
        );
    }
}

