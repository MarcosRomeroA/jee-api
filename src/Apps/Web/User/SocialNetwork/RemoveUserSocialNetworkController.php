<?php

declare(strict_types=1);

namespace App\Apps\Web\User\SocialNetwork;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\RemoveUserSocialNetwork\RemoveUserSocialNetworkCommand;
use Symfony\Component\HttpFoundation\Response;

final class RemoveUserSocialNetworkController extends ApiController
{
    public function __invoke(string $socialNetworkId, string $sessionId): Response
    {
        $command = new RemoveUserSocialNetworkCommand($sessionId, $socialNetworkId);

        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
