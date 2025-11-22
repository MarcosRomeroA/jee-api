<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchStatus;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchTournamentStatusQuery implements Query
{
    public function __construct()
    {
    }
}
