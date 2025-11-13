<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\HealthCheck;

use Behat\Behat\Context\Context;

final class HealthCheckTestContext implements Context
{
    /** @BeforeScenario @healthcheck */
    public function createTestData(): void
    {
        // HealthCheck no requiere datos de prueba
        // Es solo para verificar que la API está funcionando
    }

    /** @AfterScenario @healthcheck */
    public function cleanupTestData(): void
    {
        // No hay datos que limpiar
    }
}

