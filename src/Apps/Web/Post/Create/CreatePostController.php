<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Create\CreatePostCommand;
use App\Contexts\Web\Post\Domain\Exception\InvalidVideoMimeTypeException;
use App\Contexts\Web\Post\Domain\Exception\VideoDurationExceededException;
use App\Contexts\Web\Post\Domain\Exception\VideoFileSizeExceededException;
use App\Contexts\Web\Post\Domain\Exception\VideoResolutionExceededException;
use App\Contexts\Web\Post\Domain\PostResource;
use App\Contexts\Web\Post\Domain\Video\VideoValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatePostController extends ApiController
{
    public function __invoke(
        Request $request,
        string $id,
        string $sessionId,
        VideoValidator $videoValidator,
    ): Response {
        $input = CreatePostRequest::fromHttp($request, $id, $sessionId);
        $this->validateRequest($input);

        // Save uploaded files to temp directory and get generated resource IDs
        $resourceIds = $this->saveFilesToTemp($id, $input->getFiles(), $videoValidator);

        $command = new CreatePostCommand(
            $input->id,
            $input->body,
            $resourceIds,
            $input->sharedPostId,
            $input->sessionId,
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }

    /**
     * @param UploadedFile[] $files
     * @return string[] Generated resource UUIDs
     */
    private function saveFilesToTemp(string $postId, array $files, VideoValidator $videoValidator): array
    {
        if (empty($files)) {
            return [];
        }

        $resourceIds = [];
        $projectDir = $this->getParameter('kernel.project_dir');
        $dateFolder = (new \DateTimeImmutable())->format('Ymd');

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            // Determine resource type from mime type
            $mimeType = $file->getMimeType() ?? '';
            $type = $this->getResourceTypeFromMime($mimeType);

            PostResource::checkIsValidResourceType($type);

            // Validate video files
            if ($type === 'video') {
                $this->validateVideoFile($file, $mimeType, $videoValidator);
            }

            // Generate UUID for this resource
            $resourceId = Uuid::random();
            $resourceIds[] = $resourceId->value();

            $fileName = $resourceId->value() . '.' . $file->getClientOriginalExtension();
            $uploadDir = $projectDir . '/var/tmp/resource/' . $dateFolder . '/' . $postId . '/' . $type;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $file->move($uploadDir, $fileName);

            // Validate video duration and resolution after moving (need file path for ffprobe)
            if ($type === 'video') {
                $filePath = $uploadDir . '/' . $fileName;
                $this->validateVideoMetadata($filePath, $uploadDir, $videoValidator);
            }
        }

        return $resourceIds;
    }

    private function validateVideoFile(UploadedFile $file, string $mimeType, VideoValidator $videoValidator): void
    {
        // Validate MIME type
        if (!$videoValidator->isValidMimeType($mimeType)) {
            throw new InvalidVideoMimeTypeException($mimeType);
        }

        // Validate file size
        $fileSize = $file->getSize();
        if (!$videoValidator->isValidFileSize($fileSize)) {
            throw new VideoFileSizeExceededException($fileSize, VideoValidator::MAX_FILE_SIZE_BYTES);
        }
    }

    private function validateVideoMetadata(string $filePath, string $uploadDir, VideoValidator $videoValidator): void
    {
        // Validate duration
        $duration = $videoValidator->getDuration($filePath);
        if ($duration > VideoValidator::MAX_DURATION_SECONDS) {
            // Clean up the file before throwing
            @unlink($filePath);
            @rmdir($uploadDir);
            throw new VideoDurationExceededException($duration, VideoValidator::MAX_DURATION_SECONDS);
        }

        // Validate resolution
        $resolution = $videoValidator->getResolution($filePath);
        if ($resolution['height'] > VideoValidator::MAX_HEIGHT) {
            // Clean up the file before throwing
            @unlink($filePath);
            @rmdir($uploadDir);
            throw new VideoResolutionExceededException(
                $resolution['width'],
                $resolution['height'],
                VideoValidator::MAX_HEIGHT
            );
        }
    }

    private function getResourceTypeFromMime(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'image'; // default
    }
}
