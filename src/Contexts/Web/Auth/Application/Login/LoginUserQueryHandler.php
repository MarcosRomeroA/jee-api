<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\Login;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Auth\Application\Shared\LoginUserResponse;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;

readonly class LoginUserQueryHandler implements QueryHandler
{
    public function __construct(
        private UserAuthenticator $userAuthenticator
    )
    {
    }

    public function __invoke(LoginUserQuery $query): LoginUserResponse
    {
        $email = new EmailValue($query->email);
        return $this->userAuthenticator->__invoke($email, $query->password);
    }
}