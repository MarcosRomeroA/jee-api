<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\FindPendingRequests;

use App\Contexts\Web\Team\Application\FindPendingRequests\FindPendingTeamRequestsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class FindPendingTeamRequestsRequest
{
    public function __construct(
        #[Assert\Uuid]
        public ?string $teamId = null,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        return new self(
            teamId: $request->query->get('teamId'),
        );
    }

    public function toQuery(): FindPendingTeamRequestsQuery
    {
        return new FindPendingTeamRequestsQuery(
            teamId: $this->teamId,
        );
    }
}
