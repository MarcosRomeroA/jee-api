<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Create\CreatePostCommand;
use App\Contexts\Web\Post\Domain\PostResource;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatePostController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        $input = CreatePostRequest::fromHttp($request, $id, $sessionId);
        $this->validateRequest($input);

        // Save uploaded files to temp directory and get generated resource IDs
        $resourceIds = $this->saveFilesToTemp($id, $input->getFiles());

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
    private function saveFilesToTemp(string $postId, array $files): array
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

            // Generate UUID for this resource
            $resourceId = Uuid::random();
            $resourceIds[] = $resourceId->value();

            $fileName = $resourceId->value() . '.' . $file->getClientOriginalExtension();
            $uploadDir = $projectDir . '/var/tmp/resource/' . $dateFolder . '/' . $postId . '/' . $type;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $file->move($uploadDir, $fileName);
        }

        return $resourceIds;
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
