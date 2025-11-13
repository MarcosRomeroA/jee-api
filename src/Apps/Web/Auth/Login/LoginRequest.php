<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Login;

use App\Contexts\Web\Auth\Application\Login\LoginUserQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $password,
    ) {}

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );
    }

    public function toQuery(): LoginUserQuery
    {
        return new LoginUserQuery($this->email, $this->password);
    }
}

