<?php declare(strict_types=1);

namespace App\Apps\Web\Team\FindPendingRequests;

use App\Contexts\Web\Team\Application\FindPendingRequests\FindPendingTeamRequestsQuery;
use Symfony\Component\HttpFoundation\Request;

final readonly class FindPendingTeamRequestsRequest
{
    public function __construct() {}

    public static function fromHttp(Request $request): self
    {
        return new self();
    }

    public function toQuery(): FindPendingTeamRequestsQuery
    {
        return new FindPendingTeamRequestsQuery();
    }
}
