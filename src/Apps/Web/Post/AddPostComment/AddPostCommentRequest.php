<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostComment;
use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AddPostCommentRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $commentId;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $commentBody;
}