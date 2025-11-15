<?php declare(strict_types=1);

namespace App\Apps\Web\User\Search;

use App\Contexts\Web\User\Application\Search\SearchUsersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchUsersRequest
{
    public function __construct(
        #[Assert\Type("string")] public ?string $q = null,
        #[Assert\Type("int")] public ?int $limit = null,
        #[Assert\Type("int")] public ?int $offset = null,
    ) {}

    public static function fromHttp(Request $request): self
    {
        return new self(
            $request->query->get("q"),
            $request->query->get("limit")
                ? (int) $request->query->get("limit")
                : null,
            $request->query->get("offset")
                ? (int) $request->query->get("offset")
                : null,
        );
    }

    public function toQuery(): SearchUsersQuery
    {
        $criteria = [];

        if ($this->q !== null) {
            $criteria["username"] = $this->q;
        }

        if ($this->limit !== null) {
            $criteria["limit"] = $this->limit;
        }

        if ($this->offset !== null) {
            $criteria["offset"] = $this->offset;
        }

        return new SearchUsersQuery($criteria);
    }
}
