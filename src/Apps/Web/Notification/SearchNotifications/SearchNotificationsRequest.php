<?php declare(strict_types=1);

namespace App\Apps\Web\Notification\SearchNotifications;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchNotificationsRequest extends BaseRequest
{
    #[Assert\Type("array")]
    public mixed $q;

    #[Assert\Type("integer")]
    #[Assert\GreaterThan(0)]
    #[Assert\LessThanOrEqual(50)]
    public mixed $limit;

    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(0)]
    public mixed $offset;
}
