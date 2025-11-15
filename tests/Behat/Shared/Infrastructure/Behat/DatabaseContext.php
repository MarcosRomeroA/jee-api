<?php declare(strict_types=1);

namespace App\Tests\Behat\Shared\Infrastructure\Behat;

use Behat\Behat\Context\Context;
use Symfony\Component\Process\Process;

final class DatabaseContext implements Context
{
    private static bool $initialized = false;

    /** @BeforeSuite */
    public static function setupDatabase(): void
    {
        if (self::$initialized) {
            return;
        }

        echo "\n🔧 Inicializando base de datos para tests...\n";

        // Crear la base de datos si no existe y ejecutar migraciones
        $createResult = self::executeCommand([
            "php",
            "bin/console",
            "doctrine:database:create",
            "--if-not-exists",
            "--env=test",
        ]);

        if ($createResult["success"]) {
            echo "✓ Base de datos verificada/creada\n";
        }

        // Ejecutar migraciones
        $migrateResult = self::executeCommand([
            "php",
            "bin/console",
            "doctrine:migrations:migrate",
            "--no-interaction",
            "--env=test",
        ]);

        if ($migrateResult["success"]) {
            echo "✓ Migraciones ejecutadas\n";
        } else {
            echo "✗ Error al ejecutar migraciones\n";
            echo $migrateResult["error"];
        }

        echo "✅ Base de datos inicializada correctamente\n\n";

        self::$initialized = true;
    }

    /**
     * @param array<string> $command
     * @return array{success: bool, error: string}
     */
    private static function executeCommand(array $command): array
    {
        $process = new Process($command);
        $process->setTimeout(300); // 5 minutos timeout
        $process->run();

        return [
            "success" => $process->isSuccessful(),
            "error" => $process->getErrorOutput(),
        ];
    }
}
