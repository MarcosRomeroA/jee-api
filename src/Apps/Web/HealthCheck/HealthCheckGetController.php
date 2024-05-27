<?php declare(strict_types=1);

namespace App\Apps\Web\HealthCheck;

use Symfony\Component\HttpFoundation\JsonResponse;

class HealthCheckGetController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'Status' => 'OK',
        ]);
    }
}