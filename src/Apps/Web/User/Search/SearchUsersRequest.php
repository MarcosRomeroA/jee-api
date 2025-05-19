<?php declare(strict_types=1);

namespace App\Apps\Web\User\Search;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchUsersRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("array")]
    public mixed $q;
}