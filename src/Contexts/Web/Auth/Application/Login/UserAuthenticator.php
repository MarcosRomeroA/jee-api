<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\Login;

use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use App\Contexts\Shared\Infrastructure\Jwt\MercureJwtGenerator;
use App\Contexts\Web\Auth\Application\Shared\LoginUserResponse;
use App\Contexts\Web\Auth\Domain\Exception\EmailNotVerifiedException;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;

final readonly class UserAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        private JwtGenerator $jwtGenerator,
    ) {
    }

    public function __invoke(
        EmailValue $email,
        string $password,
    ): LoginUserResponse {
        try {
            $user = $this->userRepository->findByEmail($email->value());

            if (!$user->getPassword()->verifyPassword($password)) {
                throw new UnauthorizedException();
            }
        } catch (UserNotFoundException $e) {
            // Don't reveal whether user exists or not - return generic unauthorized
            throw new UnauthorizedException();
        }

        // Check if email is verified
        if (!$user->isVerified()) {
            throw new EmailNotVerifiedException();
        }

        // Check if user is disabled
        if ($user->isDisabled()) {
            throw new UnauthorizedException();
        }

        $token = $this->jwtGenerator->create([
            "id" => $user->getId()->value(),
        ]);

        $refreshToken = $this->jwtGenerator->create(
            [
                "id" => $user->getId()->value(),
            ],
            true,
        );

        $notificationToken = MercureJwtGenerator::create(
            $_ENV["APP_URL"] . "/notification/" . $user->getId()->value(),
        );

        return new LoginUserResponse(
            $user->getId()->value(),
            $notificationToken,
            $token,
            $refreshToken,
        );
    }
}
