<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchStatus;

use App\Contexts\Web\Tournament\Application\SearchStatus\SearchTournamentStatusQuery;
use Symfony\Component\HttpFoundation\Request;

final readonly class SearchTournamentStatusRequest
{
    public function __construct()
    {
    }

    public static function fromHttp(Request $request): self
    {
        return new self();
    }

    public function toQuery(): SearchTournamentStatusQuery
    {
        return new SearchTournamentStatusQuery();
    }
}
