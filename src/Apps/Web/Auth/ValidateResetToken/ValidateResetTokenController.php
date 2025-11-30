<?php

declare(strict_types=1);

namespace App\Apps\Web\Auth\ValidateResetToken;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Auth\Application\ValidateResetToken\ValidateResetTokenQuery;
use Symfony\Component\HttpFoundation\Response;

final class ValidateResetTokenController extends ApiController
{
    public function __invoke(string $token): Response
    {
        $query = new ValidateResetTokenQuery(token: $token);
        $response = $this->ask($query);

        return $this->successResponse($response);
    }
}
