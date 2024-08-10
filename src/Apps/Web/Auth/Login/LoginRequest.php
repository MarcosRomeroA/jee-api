<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Login;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $email;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $password;
}