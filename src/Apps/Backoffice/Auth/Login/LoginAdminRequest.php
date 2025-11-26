<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Auth\Login;

use App\Contexts\Backoffice\Auth\Application\Login\LoginAdminQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginAdminRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $user,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $password,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['user'] ?? '',
            $data['password'] ?? ''
        );
    }

    public function toQuery(): LoginAdminQuery
    {
        return new LoginAdminQuery($this->user, $this->password);
    }
}
