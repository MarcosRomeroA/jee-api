<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Shared;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final readonly class TournamentImageUploader
{
    public function __construct(
        private FileManager $fileManager,
        private KernelInterface $kernel,
    ) {
    }

    /**
     * @throws Exception
     */
    public function uploadBase64Image(string $tournamentId, string $base64Image): string
    {
        // Verificar si la imagen está en formato base64
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            throw new Exception('Invalid base64 image format');
        }

        $imageType = $matches[1];
        $base64Data = substr($base64Image, strpos($base64Image, ',') + 1);
        $imageData = base64_decode($base64Data);

        if ($imageData === false) {
            throw new Exception('Failed to decode base64 image');
        }

        // Generar nombre de archivo único
        $filename = uniqid() . '.' . $imageType;
        $tempDir = $this->kernel->getProjectDir() . '/var/tmp/tournament/' . $tournamentId;
        $filesystem = new Filesystem();

        if (!$filesystem->exists($tempDir)) {
            $filesystem->mkdir($tempDir, 0755);
        }

        $tempFilePath = $tempDir . '/' . $filename;

        // Guardar archivo temporalmente
        if (file_put_contents($tempFilePath, $imageData) === false) {
            throw new Exception('Failed to write temporary image file');
        }

        try {
            // Calcular checksum MD5
            $md5Checksum = base64_encode(md5_file($tempFilePath, true));

            // Subir a S3
            $this->fileManager->upload(
                $tempDir,
                'tournament/' . $tournamentId,
                $filename,
                $md5Checksum
            );

            return $filename;
        } catch (Exception $e) {
            // Limpiar archivo temporal en caso de error
            if ($filesystem->exists($tempFilePath)) {
                $filesystem->remove($tempFilePath);
            }
            throw $e;
        }
    }
}
