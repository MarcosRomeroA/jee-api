<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Search;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchPostsRequest extends BaseRequest
{
    #[Assert\Type("array")]
    public mixed $q;
}
