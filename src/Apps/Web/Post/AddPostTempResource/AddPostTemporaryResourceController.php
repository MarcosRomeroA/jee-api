<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostTempResource;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Domain\Exception\InvalidVideoMimeTypeException;
use App\Contexts\Web\Post\Domain\Exception\VideoDurationExceededException;
use App\Contexts\Web\Post\Domain\Exception\VideoFileSizeExceededException;
use App\Contexts\Web\Post\Domain\Exception\VideoResolutionExceededException;
use App\Contexts\Web\Post\Domain\Video\VideoValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddPostTemporaryResourceController extends ApiController
{
    public function __invoke(
        Request $request,
        string $id,
        string $sessionId,
        VideoValidator $videoValidator,
    ): Response {
        $input = AddPostTemporaryResourceRequest::fromHttp($request);
        $this->validateRequest($input);

        // Validate video before processing
        if ($input->type === 'video' && $input->resource instanceof UploadedFile) {
            $this->validateVideoUpload($input->resource, $videoValidator);
        }

        $command = $input->toCommand($id, $this->getParameter('kernel.project_dir'));

        $this->dispatch($command);

        // Validate video metadata after file is saved (needs file path for ffprobe)
        if ($input->type === 'video') {
            $this->validateVideoMetadataAfterSave($command, $videoValidator);
        }

        return $this->successEmptyResponse(Response::HTTP_ACCEPTED);
    }

    private function validateVideoUpload(UploadedFile $file, VideoValidator $videoValidator): void
    {
        $mimeType = $file->getMimeType() ?? '';

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

    private function validateVideoMetadataAfterSave($command, VideoValidator $videoValidator): void
    {
        $dateFolder = (new \DateTimeImmutable())->format('Ymd');
        $projectDir = $this->getParameter('kernel.project_dir');

        // Find the saved file
        $uploadDir = "$projectDir/var/tmp/resource/$dateFolder/{$command->postId}/video";
        $pattern = "$uploadDir/{$command->resourceId}.*";
        $files = glob($pattern);

        if (empty($files)) {
            return;
        }

        $filePath = $files[0];

        // Validate duration
        $duration = $videoValidator->getDuration($filePath);
        if ($duration > VideoValidator::MAX_DURATION_SECONDS) {
            @unlink($filePath);
            throw new VideoDurationExceededException($duration, VideoValidator::MAX_DURATION_SECONDS);
        }

        // Validate resolution
        $resolution = $videoValidator->getResolution($filePath);
        if ($resolution['height'] > VideoValidator::MAX_HEIGHT) {
            @unlink($filePath);
            throw new VideoResolutionExceededException(
                $resolution['width'],
                $resolution['height'],
                VideoValidator::MAX_HEIGHT
            );
        }
    }
}
