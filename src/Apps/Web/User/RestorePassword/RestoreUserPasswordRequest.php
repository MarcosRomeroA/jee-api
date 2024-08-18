<?php declare(strict_types=1);

namespace App\Apps\Web\User\RestorePassword;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class RestoreUserPasswordRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $newPassword;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $confirmationNewPassword;
}