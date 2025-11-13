<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Web\Post\Application\Create\CreatePostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePostRequest
{
    public function __construct(
        public string $id,
        public string $sessionId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $body,

        #[Assert\Type("array")]
        public array $resources = [],

        #[Assert\Type("string")]
        public ?string $sharedPostId = null,
    ) {}

    public static function fromHttp(Request $request, string $id, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $sessionId,
            $data['body'] ?? '',
            $data['resources'] ?? [],
            $data['sharedPostId'] ?? null
        );
    }

    public function toCommand(): CreatePostCommand
    {
        return new CreatePostCommand(
            $this->id,
            $this->body,
            $this->resources,
            $this->sharedPostId,
            $this->sessionId
        );
    }
}

