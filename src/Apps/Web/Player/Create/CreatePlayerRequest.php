<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Create;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePlayerRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $id;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $userId;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $gameId;

    #[Assert\NotNull, Assert\Type("array")]
    public mixed $gameRoleIds;

    #[Assert\Type("string")]
    public mixed $gameRankId;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $username;
}

