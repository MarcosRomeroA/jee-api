<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Refresh;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetTokenByRefreshRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $refreshToken;
}