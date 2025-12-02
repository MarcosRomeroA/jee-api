<?php

declare(strict_types=1);

namespace App\Apps\Web\Player\VerifyRank;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Player\Application\VerifyRank\VerifyPlayerRankCommand;
use Symfony\Component\HttpFoundation\Response;

final class VerifyPlayerRankController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $command = new VerifyPlayerRankCommand($id);
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
