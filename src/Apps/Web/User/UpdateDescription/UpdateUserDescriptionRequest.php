<?php

declare(strict_types=1);

namespace App\Apps\Web\User\UpdateDescription;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateUserDescriptionRequest extends BaseRequest
{
    #[Assert\Type("string")]
    public mixed $description;
}
