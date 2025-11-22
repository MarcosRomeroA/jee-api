<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostTempResource;

use App\Contexts\Web\Post\Application\AddPostTempResource\AddPostTempResourceCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AddPostTemporaryResourceRequest
{
    public function __construct(
        #[Assert\NotBlank(message: "Resource id is required")]
        #[Assert\Uuid(message: "Resource id must be a valid UUID")]
        public ?string $id,

        #[Assert\NotBlank(message: "Resource type is required")]
        #[Assert\Choice(choices: ["image", "video"], message: "Resource type must be 'image' or 'video'")]
        public ?string $type,

        #[Assert\NotNull(message: "Resource file is required")]
        public ?UploadedFile $resource,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        $data = $request->request->all();

        return new self(
            $data['id'] ?? null,
            $data['type'] ?? null,
            $request->files->get('resource'),
        );
    }

    public function toCommand(string $postId, string $projectDir): AddPostTempResourceCommand
    {
        return new AddPostTempResourceCommand(
            $this->id,
            $postId,
            $this->type,
            $this->resource,
            $projectDir,
        );
    }
}
