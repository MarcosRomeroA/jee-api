<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Admin\Search;

use App\Contexts\Backoffice\Admin\Application\Search\SearchAdminsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchAdminsRequest
{
    public function __construct(
        #[Assert\Type('string')]
        public ?string $name = null,
        #[Assert\Type('string')]
        public ?string $user = null,
        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public int $limit = 10,
        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public int $offset = 0,
        #[Assert\Type('bool')]
        public bool $includeDeleted = false,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        return new self(
            $request->query->get('name'),
            $request->query->get('user'),
            (int) ($request->query->get('limit', 10)),
            (int) ($request->query->get('offset', 0)),
            filter_var($request->query->get('includeDeleted', false), FILTER_VALIDATE_BOOLEAN),
        );
    }

    public function toQuery(): SearchAdminsQuery
    {
        $criteria = [
            'limit' => $this->limit,
            'offset' => $this->offset,
            'includeDeleted' => $this->includeDeleted,
        ];

        if ($this->name !== null) {
            $criteria['name'] = $this->name;
        }

        if ($this->user !== null) {
            $criteria['user'] = $this->user;
        }

        return new SearchAdminsQuery($criteria);
    }
}
