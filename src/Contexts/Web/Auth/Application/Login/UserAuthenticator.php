<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\Login;

use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use App\Contexts\Web\Auth\Application\Shared\LoginUserResponse;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;

final readonly class UserAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        private JwtGenerator   $jwtGenerator
    )
    {
    }

    public function __invoke(
        EmailValue $email,
        string $password
    ): LoginUserResponse
    {
        $user = $this->userRepository->findByEmail($email->value());

        if (!$user->getPassword()->verifyPassword($password)){
            throw new UnauthorizedException();
        }

        $token = $this->jwtGenerator->create([
            "id" => $user->getId()->value(),
        ]);

        $refreshToken = $this->jwtGenerator->create([
            "id" => $user->getId()->value(),
        ], true);

        return new LoginUserResponse(
            $user->getId()->value(),
            $token,
            $refreshToken
        );
    }
}