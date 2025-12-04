<?php

declare(strict_types=1);

namespace App\Apps\Web\Event\Search;

use App\Contexts\Web\Event\Application\Search\SearchEventsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchEventsRequest
{
    public function __construct(
        #[Assert\Uuid]
        public ?string $gameId,
        #[Assert\Choice(choices: ['presencial', 'virtual'])]
        public ?string $type,
        #[Assert\Type('integer')]
        #[Assert\Positive]
        public int $limit,
        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public int $offset,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        return new self(
            $request->query->get('gameId'),
            $request->query->get('type'),
            (int) $request->query->get('limit', 10),
            (int) $request->query->get('offset', 0),
        );
    }

    public function toQuery(): SearchEventsQuery
    {
        return new SearchEventsQuery(
            $this->gameId,
            $this->type,
            $this->limit,
            $this->offset,
        );
    }
}
