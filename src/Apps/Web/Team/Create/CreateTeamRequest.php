<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Create;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTeamRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $id;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $gameId;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $ownerId;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $name;

    #[Assert\Type("string")]
    public mixed $image;
}

