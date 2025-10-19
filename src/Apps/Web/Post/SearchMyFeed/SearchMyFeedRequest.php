<?php declare(strict_types=1);

namespace App\Apps\Web\Post\SearchMyFeed;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchMyFeedRequest extends BaseRequest
{
    #[Assert\Type("array")]
    public mixed $q;
}