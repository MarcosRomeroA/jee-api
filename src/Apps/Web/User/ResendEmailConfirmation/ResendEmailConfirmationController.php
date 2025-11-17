<?php

declare(strict_types=1);

namespace App\Apps\Web\User\ResendEmailConfirmation;

use App\Apps\Web\User\ResendEmailConfirmation\Request\ResendEmailConfirmationRequest;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResendEmailConfirmationController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $resendRequest = ResendEmailConfirmationRequest::fromHttp($request);

        $this->validateRequest($resendRequest);

        $command = $resendRequest->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
