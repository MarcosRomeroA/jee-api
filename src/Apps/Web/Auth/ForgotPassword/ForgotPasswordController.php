<?php

declare(strict_types=1);

namespace App\Apps\Web\Auth\ForgotPassword;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ForgotPasswordController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = ForgotPasswordRequest::fromHttp($request);
        $this->validateRequest($input);
        $command = $input->toCommand();
        $this->dispatch($command);

        // Siempre responder 200 para no revelar si el email existe
        return $this->successEmptyResponse();
    }
}
