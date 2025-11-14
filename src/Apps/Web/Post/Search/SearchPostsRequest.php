<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Search;

use App\Contexts\Web\Post\Application\Search\SearchPostQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchPostsRequest
{
    public function __construct(
        #[Assert\Type("array")] public ?array $q = null,
    ) {}

    public static function fromHttp(Request $request): self
    {
        $q = $request->query->get("q");
        return new self($q ? ["q" => $q] : null);
    }

    public function toQuery(): SearchPostQuery
    {
        return new SearchPostQuery($this->q);
    }
}
