<?php declare(strict_types=1);

namespace App\Apps\Web\User\UpdatePassword;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateUserPasswordRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $oldPassword;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $newPassword;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $confirmationNewPassword;
}