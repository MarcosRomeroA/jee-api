<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Search;

use App\Contexts\Web\Post\Application\Search\SearchPostQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchPostsRequest
{
    public function __construct(
        #[Assert\Type("array")] public ?array $criteria = null,
        public ?string $currentUserId = null,
    ) {}

    public static function fromHttp(Request $request, ?string $sessionId = null): self
    {
        $criteria = [];

        $q = $request->query->get("q");
        if ($q) {
            $criteria["q"] = $q;
        }

        $username = $request->query->get("username");
        if ($username) {
            $criteria["username"] = $username;
        }

        $userId = $request->query->get("userId");
        if ($userId) {
            $criteria["userId"] = $userId;
        }

        return new self(!empty($criteria) ? $criteria : null, $sessionId);
    }

    public function toQuery(): SearchPostQuery
    {
        return new SearchPostQuery($this->criteria, $this->currentUserId);
    }
}
