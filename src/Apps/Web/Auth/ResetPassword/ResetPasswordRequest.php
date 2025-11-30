<?php

declare(strict_types=1);

namespace App\Apps\Web\Auth\ResetPassword;

use App\Contexts\Web\Auth\Application\ResetPassword\ResetPasswordCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ResetPasswordRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Token is required')]
        public string $token,
        #[Assert\NotBlank(message: 'Password is required')]
        #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
        public string $password,
        #[Assert\NotBlank(message: 'Password confirmation is required')]
        public string $passwordConfirmation,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            token: $data['token'] ?? '',
            password: $data['password'] ?? '',
            passwordConfirmation: $data['password_confirmation'] ?? '',
        );
    }

    public function toCommand(): ResetPasswordCommand
    {
        return new ResetPasswordCommand(
            token: $this->token,
            password: $this->password,
            passwordConfirmation: $this->passwordConfirmation,
        );
    }
}
