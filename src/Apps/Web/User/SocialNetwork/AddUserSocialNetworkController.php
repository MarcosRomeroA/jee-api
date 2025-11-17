<?php

declare(strict_types=1);

namespace App\Apps\Web\User\SocialNetwork;

use App\Apps\Web\User\SocialNetwork\Request\AddUserSocialNetworkRequest;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class AddUserSocialNetworkController extends ApiController
{
    public function __invoke(string $socialNetworkId, string $username, string $sessionId): Response
    {
        $addRequest = AddUserSocialNetworkRequest::fromHttp($socialNetworkId, $username, $sessionId);

        $this->validateRequest($addRequest);

        $command = $addRequest->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
