<?php declare(strict_types=1);

namespace App\Apps\Web\Team\UpdateLeader;

use App\Contexts\Web\Team\Application\UpdateLeader\UpdateTeamLeaderCommand;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTeamLeaderRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $teamId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $newLeaderId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $requesterId,
    ) {}

    public function toCommand(): UpdateTeamLeaderCommand
    {
        return new UpdateTeamLeaderCommand(
            $this->teamId,
            $this->newLeaderId,
            $this->requesterId
        );
    }
}
