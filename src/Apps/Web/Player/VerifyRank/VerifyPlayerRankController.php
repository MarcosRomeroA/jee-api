<?php declare(strict_types=1);

namespace App\Apps\Web\Player\VerifyRank;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Player\Domain\Service\RankVerifier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VerifyPlayerRankController extends ApiController
{
    public function __construct(
        private readonly RankVerifier $rankVerifier
    ) {
    }

    #[Route('/api/player/verify-rank', name: 'verify_player_rank', methods: ['POST'])]
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
            $rankInfo = $this->rankVerifier->getRankInfo($username, $gameIdentifier);

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

