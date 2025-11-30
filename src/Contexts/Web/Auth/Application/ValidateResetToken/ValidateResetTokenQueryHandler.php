<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ValidateResetToken;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class ValidateResetTokenQueryHandler implements QueryHandler
{
    public function __construct(
        private ResetTokenValidator $validator,
    ) {
    }

    public function __invoke(ValidateResetTokenQuery $query): ValidateResetTokenResponse
    {
        return $this->validator->__invoke($query->token);
    }
}
