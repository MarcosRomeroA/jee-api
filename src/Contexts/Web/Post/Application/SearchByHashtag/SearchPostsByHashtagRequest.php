<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchByHashtag;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchPostsByHashtagRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 100)]
        public string $hashtag,

        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public int $page = 1,

        #[Assert\Type('int')]
        #[Assert\Positive]
        #[Assert\LessThanOrEqual(100)]
        public int $limit = 20
    ) {
    }

    public static function fromHttp(Request $request, string $hashtag): self
    {
        return new self(
            hashtag: $hashtag,
            page: (int) $request->query->get('page', 1),
            limit: (int) $request->query->get('limit', 20)
        );
    }

    public function toQuery(): SearchPostsByHashtagQuery
    {
        return new SearchPostsByHashtagQuery(
            $this->hashtag,
            $this->page,
            $this->limit
        );
    }
}
