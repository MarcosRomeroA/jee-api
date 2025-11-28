<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Hashtag\Search;

use App\Contexts\Backoffice\Hashtag\Application\Search\SearchHashtagsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchHashtagsRequest
{
    public function __construct(
        #[Assert\Type('string')]
        public ?string $q = null,
        #[Assert\Type('string')]
        public ?string $tag = null,
        #[Assert\Type('bool')]
        public ?bool $disabled = null,
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public int $limit = 20,
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public int $offset = 0,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        $disabled = $request->query->get('disabled');
        $disabledBool = null;
        if ($disabled !== null) {
            $disabledBool = filter_var($disabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return new self(
            q: $request->query->get('q'),
            tag: $request->query->get('tag'),
            disabled: $disabledBool,
            limit: min($request->query->getInt('limit', 20), 100),
            offset: $request->query->getInt('offset', 0),
        );
    }

    public function toQuery(): SearchHashtagsQuery
    {
        $criteria = [
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];

        if ($this->q !== null) {
            $criteria['q'] = $this->q;
        }

        if ($this->tag !== null) {
            $criteria['tag'] = $this->tag;
        }

        if ($this->disabled !== null) {
            $criteria['disabled'] = $this->disabled;
        }

        return new SearchHashtagsQuery($criteria);
    }
}
