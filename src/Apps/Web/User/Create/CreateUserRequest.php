<?php declare(strict_types=1);

namespace App\Apps\Web\User\Create;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateUserRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $id;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $firstname;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $lastname;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $username;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $email;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $password;
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $confirmationPassword;
}