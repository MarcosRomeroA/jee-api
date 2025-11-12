<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Update;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTeamRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $name;

    #[Assert\Type("string")]
    public mixed $image;
}

