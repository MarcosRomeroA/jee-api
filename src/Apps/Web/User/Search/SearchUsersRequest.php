<?php declare(strict_types=1);

namespace App\Apps\Web\User\Search;

use App\Contexts\Web\User\Application\Search\SearchUsersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchUsersRequest
{
    public function __construct(
        #[Assert\Type("array")]
        public ?array $q = null,
    ) {}

    public static function fromHttp(Request $request): self
    {
        return new self(
            $request->query->all('q')
        );
    }

    public function toQuery(): SearchUsersQuery
    {
        return new SearchUsersQuery($this->q);
    }
}

