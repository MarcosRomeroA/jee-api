<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePostRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $id;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $body;
}