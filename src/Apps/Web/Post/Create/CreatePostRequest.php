<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePostRequest
{
    /**
     * @param UploadedFile[] $files
     */
    public function __construct(
        public string $id,
        public string $sessionId,
        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $body,
        #[Assert\Type("string")]
        public ?string $sharedPostId = null,
        public array $files = [],
    ) {
    }

    public static function fromHttp(Request $request, string $id, string $sessionId): self
    {
        $files = $request->files->all();

        // Always check form-data first (multipart/form-data)
        $body = $request->request->get('body');
        $sharedPostId = $request->request->get('sharedPostId');

        // If body is present in form-data, use form-data values
        if ($body !== null) {
            // Handle empty strings and literal "null" as null
            if (empty($sharedPostId) || $sharedPostId === 'null') {
                $sharedPostId = null;
            }

            return new self(
                $id,
                $sessionId,
                $body,
                $sharedPostId,
                $files,
            );
        }

        // Fallback to JSON (for backwards compatibility)
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            $id,
            $sessionId,
            $data['body'] ?? '',
            $data['sharedPostId'] ?? null,
            $files,
        );
    }

    /**
     * @return UploadedFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
