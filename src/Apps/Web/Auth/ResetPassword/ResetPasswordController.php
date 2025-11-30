<?php

declare(strict_types=1);

namespace App\Apps\Web\Auth\ResetPassword;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResetPasswordController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = ResetPasswordRequest::fromHttp($request);
        $this->validateRequest($input);
        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
