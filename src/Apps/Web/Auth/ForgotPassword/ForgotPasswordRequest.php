<?php

declare(strict_types=1);

namespace App\Apps\Web\Auth\ForgotPassword;

use App\Contexts\Web\Auth\Application\ForgotPassword\ForgotPasswordCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ForgotPasswordRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required')]
        #[Assert\Email(message: 'Invalid email format')]
        public string $email,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            email: $data['email'] ?? '',
        );
    }

    public function toCommand(): ForgotPasswordCommand
    {
        return new ForgotPasswordCommand(
            email: $this->email,
        );
    }
}
