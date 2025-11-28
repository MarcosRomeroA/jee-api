<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\FindBatch;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\FindBatch\FindPostsByIdsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FindPostsByIdsController extends ApiController
{
    public function __invoke(Request $request, ?string $sessionId = null): Response
    {
        $ids = $request->query->all('ids');

        // Handle both ?ids[]=uuid1&ids[]=uuid2 and ?ids=uuid1,uuid2
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        // Clean up the array
        $ids = array_filter(array_map('trim', $ids));

        $query = new FindPostsByIdsQuery($ids, $sessionId);

        $response = $this->queryBus->ask($query);

        return $this->successResponse(['data' => $response]);
    }
}
