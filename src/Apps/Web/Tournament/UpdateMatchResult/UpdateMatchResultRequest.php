<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\UpdateMatchResult;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateMatchResultRequest extends BaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    public mixed $scores; // Array de team_id => score

    #[Assert\Uuid]
    public mixed $winnerId;
}

