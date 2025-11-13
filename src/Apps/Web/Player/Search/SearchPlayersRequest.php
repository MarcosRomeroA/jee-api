<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Search;

use App\Contexts\Web\Player\Application\Search\SearchPlayersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchPlayersRequest
{
    public function __construct(
        #[Assert\Type("string")]
        public ?string $gameId = null,

        #[Assert\Type("int")]
        #[Assert\PositiveOrZero]
        public ?int $page = null,

        #[Assert\Type("int")]
        #[Assert\Positive]
        public ?int $limit = null,
    ) {}

    public static function fromHttp(Request $request): self
    {
        return new self(
            $request->query->get('gameId'),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );
    }

    public function toQuery(): SearchPlayersQuery
    {
        return new SearchPlayersQuery(
            $this->gameId,
            $this->page,
            $this->limit
        );
    }
}

