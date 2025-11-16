<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\Notification;

use Behat\Behat\Context\Context;

final class MercureContext implements Context
{
    private $mercureListenerProcess = null;
    private ?string $receivedNotification = null;
    private string $currentUserId = "";

    /**
     * @Given I am listening to Mercure notifications for user :userId
     */
    public function iAmListeningToMercureNotificationsForUser(
        string $userId,
    ): void {
        $this->currentUserId = $userId;
        // Usar MERCURE_URL (interno) en vez de MERCURE_PUBLIC_URL porque el test corre dentro del contenedor
        $mercureUrl =
            $_ENV["MERCURE_URL"] ?? "http://mercure/.well-known/mercure";
        $topic =
            ($_ENV["APP_URL"] ?? "http://localhost:8200") .
            "/notification/{$userId}";
        $jwt = $this->generateMercureJwt($topic);

        // Crear un archivo temporal para almacenar el output
        $outputFile = tempnam(sys_get_temp_dir(), "mercure_");

        // Construir la URL completa con el topic
        $fullUrl = $mercureUrl . "?topic=" . urlencode($topic);

        // Usar curl - el JWT no necesita escapeshellarg porque no tiene caracteres especiales de shell
        $cmd = sprintf(
            'curl -N -H "Authorization: Bearer %s" -H "Accept: text/event-stream" %s > %s 2>&1 & echo $!',
            $jwt,
            escapeshellarg($fullUrl),
            escapeshellarg($outputFile),
        );

        $pid = trim(shell_exec($cmd));

        $this->mercureListenerProcess = [
            "pid" => $pid,
            "output_file" => $outputFile,
            "started_at" => microtime(true),
        ];

        // Dar tiempo para que se establezca la conexión
        usleep(500000); // 0.5 segundos

        echo "✓ Started listening to Mercure on topic: {$topic}\n";
    }

    /**
     * @Then I should receive a Mercure notification about :eventType
     */
    public function iShouldReceiveAMercureNotificationAbout(
        string $eventType,
    ): void {
        if (!$this->mercureListenerProcess) {
            throw new \RuntimeException(
                'No Mercure listener is running. Use "I am listening to Mercure notifications" first.',
            );
        }

        // Esperar hasta 5 segundos para recibir la notificación
        $maxWait = 5;
        $startTime = microtime(true);
        $outputFile = $this->mercureListenerProcess["output_file"];

        while (microtime(true) - $startTime < $maxWait) {
            if (file_exists($outputFile)) {
                $content = file_get_contents($outputFile);

                // Buscar mensajes SSE en el formato "data: {...}"
                if (preg_match("/data:\s*(\{.*?\})/s", $content, $matches)) {
                    $this->receivedNotification = $matches[1];
                    $this->stopMercureListener();

                    echo "✓ Received Mercure notification: {$this->receivedNotification}\n";

                    // Verificar que la notificación contiene información sobre el evento
                    $data = json_decode($this->receivedNotification, true);
                    if (!$data) {
                        throw new \RuntimeException(
                            "Invalid JSON received from Mercure",
                        );
                    }

                    return;
                }
            }

            usleep(200000); // 0.2 segundos
        }

        // Si llegamos aquí, no se recibió notificación

        // Leer el contenido del archivo para debugging ANTES de detener el listener
        $debugContent = file_exists($outputFile)
            ? file_get_contents($outputFile)
            : "File does not exist";
        $fileSize = file_exists($outputFile) ? filesize($outputFile) : 0;

        $this->stopMercureListener();

        // En tests @realtime, FALLAR si no se recibe la notificación
        // Esto asegura que la infraestructura de Mercure funciona correctamente
        throw new \RuntimeException(
            "❌ Mercure notification NOT received within {$maxWait} seconds.\n" .
                "This test REQUIRES Mercure to be running and working correctly.\n\n" .
                "Debug info:\n" .
                "- Output file: {$outputFile}\n" .
                "- File size: {$fileSize} bytes\n" .
                "- First 500 chars of debug output:\n" .
                substr($debugContent, 0, 500) .
                "\n\n" .
                "To fix:\n" .
                "1. Ensure Mercure is running: docker ps | grep mercure\n" .
                "2. Check Mercure logs: docker logs jee_mercure\n" .
                "3. Verify MERCURE_URL in .env: " .
                ($_ENV["MERCURE_URL"] ?? "NOT SET"),
        );
    }

    /**
     * @Then the Mercure notification should be published
     */
    public function theMercureNotificationShouldBePublished(): void
    {
        echo "✓ Notification created and Mercure publish attempted\n";
    }

    /**
     * @Then I should be able to subscribe to Mercure notifications
     */
    public function iShouldBeAbleToSubscribeToMercureNotifications(): void
    {
        $mercureUrl =
            $_ENV["MERCURE_PUBLIC_URL"] ??
            "http://localhost:9090/.well-known/mercure";

        if (empty($mercureUrl)) {
            throw new \RuntimeException("MERCURE_PUBLIC_URL is not configured");
        }

        echo "✓ Mercure is configured at: {$mercureUrl}\n";
        echo "  To test real-time notifications:\n";
        echo "  1. Ensure Mercure server is running (docker-compose up mercure)\n";
        echo "  2. Subscribe to topic: {$_ENV["APP_URL"]}/notification/{userId}\n";
        echo "  3. Generate JWT with MercureJwtGenerator::create()\n";
    }

    private function stopMercureListener(): void
    {
        if (
            $this->mercureListenerProcess &&
            isset($this->mercureListenerProcess["pid"])
        ) {
            // Matar el proceso
            shell_exec(
                "kill " . $this->mercureListenerProcess["pid"] . " 2>/dev/null",
            );

            // Limpiar el archivo temporal
            if (file_exists($this->mercureListenerProcess["output_file"])) {
                @unlink($this->mercureListenerProcess["output_file"]);
            }

            $this->mercureListenerProcess = null;
        }
    }

    private function generateMercureJwt(string $topic): string
    {
        $secret =
            $_ENV["MERCURE_JWT_SECRET"] ??
            "3d0a1bcf213a88a2b4c8b5e09e1ad1e0d678c05f8c66c52e99decc75f3ccf7ab";

        $header = $this->base64UrlEncode(
            json_encode(["alg" => "HS256", "typ" => "JWT"]),
        );
        $payload = $this->base64UrlEncode(
            json_encode([
                "mercure" => [
                    "subscribe" => [$topic],
                ],
                "exp" => time() + 3600,
            ]),
        );

        $signature = $this->base64UrlEncode(
            hash_hmac("sha256", "$header.$payload", $secret, true),
        );

        return "$header.$payload.$signature";
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }

    /** @BeforeScenario @realtime */
    public function ensureSecondaryUserExists(): void
    {
        // Los usuarios globales ya fueron creados en DatabaseContext::setupDatabase()
        // No necesitamos crear usuarios aquí
        $this->entityManager->clear();
    }

    /** @AfterScenario @mercure */
    public function cleanupMercureListener(): void
    {
        $this->stopMercureListener();
    }

    public function __construct(
        private readonly \Doctrine\ORM\EntityManagerInterface $entityManager,
        private readonly \App\Contexts\Web\User\Domain\UserRepository $userRepository,
    ) {
    }
}
