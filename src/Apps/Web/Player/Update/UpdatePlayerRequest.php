<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Update;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdatePlayerRequest extends BaseRequest
{

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $username;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $gameRoleId;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $gameRankId;
}

