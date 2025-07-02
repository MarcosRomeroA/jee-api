<?php declare(strict_types=1);

namespace App\Apps\Web\Conversation\CreateMessage;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateMessageRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $content;
}