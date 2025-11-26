<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Auth\Application\Login;

use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminUserValue;
use App\Contexts\Backoffice\Auth\Application\Shared\LoginAdminResponse;
use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class LoginAdminQueryHandler implements QueryHandler
{
    public function __construct(
        private AdminAuthenticator $authenticator,
    ) {
    }

    public function __invoke(LoginAdminQuery $query): LoginAdminResponse
    {
        $user = new AdminUserValue($query->user);
        return $this->authenticator->__invoke($user, $query->password);
    }
}
