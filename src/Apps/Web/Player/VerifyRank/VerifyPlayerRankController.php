<?php

declare(strict_types=1);

namespace App\Apps\Web\Player\VerifyRank;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Player\Application\VerifyRank\VerifyPlayerRankQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class VerifyPlayerRankController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? '';
        $gameIdentifier = $data['gameIdentifier'] ?? '';

        if (empty($username) || empty($gameIdentifier)) {
            return new JsonResponse([
                'error' => 'username and gameIdentifier are required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $query = new VerifyPlayerRankQuery($username, $gameIdentifier);
            $rankInfo = $this->queryBus->ask($query);

            return new JsonResponse([
                'verified' => true,
                'username' => $username,
                'game' => $gameIdentifier,
                'rankInfo' => $rankInfo
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'verified' => false,
                'username' => $username,
                'game' => $gameIdentifier,
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
