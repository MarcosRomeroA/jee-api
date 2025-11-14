<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchTournaments;

use App\Contexts\Web\Tournament\Application\Search\SearchTournamentsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchTournamentsRequest
{
    public function __construct(
        #[Assert\Type("string")]
        public ?string $q = null,

        #[Assert\Type("string")]
        public ?string $gameId = null,

        #[Assert\Type("bool")]
        public bool $mine = false,

        #[Assert\Type("bool")]
        public bool $open = false,

        #[Assert\Type("int")]
        public int $limit = 20,

        #[Assert\Type("int")]
        public int $offset = 0,
    ) {}

    public static function fromHttp(Request $request): self
    {
        return new self(
            $request->query->get('q'),
            $request->query->get('gameId'),
            (bool) $request->query->get('mine', false),
            (bool) $request->query->get('open', false),
            (int) $request->query->get('limit', 20),
            (int) $request->query->get('offset', 0)
        );
    }

    public function toQuery(string $userId): SearchTournamentsQuery
    {
        return new SearchTournamentsQuery(
            $this->q,
            $this->gameId,
            $this->mine ? $userId : null,
            $this->open,
            $this->limit,
            $this->offset
        );
    }
}

