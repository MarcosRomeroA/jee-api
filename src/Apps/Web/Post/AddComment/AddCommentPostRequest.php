<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddComment;
use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AddCommentPostRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $id;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $comment;
}