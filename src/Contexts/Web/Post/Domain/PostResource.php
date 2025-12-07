<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: PostResourceRepository::class)]
#[ORM\Table(name: "post_resource")]
class PostResource
{
    const int RESOURCE_TYPE_IMAGE = 1;
    const int RESOURCE_TYPE_VIDEO = 2;
    const int RESOURCE_TYPE_AUDIO = 3;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $filename;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $resourceType;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'resources')]
    private ?Post $post;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $imageUpdatedAt = null;

    use Timestamps;

    private string $url;

    public function __construct(
        Uuid $id,
        string $filename,
        int $resourceType,
    ) {
        $this->id = $id;
        $this->filename = $filename;
        $this->resourceType = $resourceType;
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
    }

    /**
     * @throws Exception
     */
    public static function getResourceTypeFromId(int $id): string {
        return match($id) {
            self::RESOURCE_TYPE_IMAGE => 'image',
            self::RESOURCE_TYPE_VIDEO => 'video',
            self::RESOURCE_TYPE_AUDIO => 'audio',
            default => throw new Exception('file_type_id_not_supported_exception'),
        };
    }

    /**
     * @throws Exception
     */
    public static function getResourceTypeFromName(string $name): int {
        return match($name) {
            'image' => self::RESOURCE_TYPE_IMAGE,
            'video' => self::RESOURCE_TYPE_VIDEO,
            'audio' => self::RESOURCE_TYPE_AUDIO,
            default => throw new Exception('file_type_name_not_supported_exception'),
        };
    }

    /**
     * @throws Exception
     */
    public static function checkIsValidResourceType(string $resourceType): bool {
        return match($resourceType) {
            'image', 'audio', 'video' => true,
            default => throw new Exception("resource_type_error_exception"),
        };
    }

    public static function getResourceTypes(): array {
        return [
            self::RESOURCE_TYPE_IMAGE => 'image',
            self::RESOURCE_TYPE_VIDEO => 'video',
            self::RESOURCE_TYPE_AUDIO => 'audio',
        ];
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getResourceType(): int
    {
        return $this->resourceType;
    }

    public function getImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->imageUpdatedAt;
    }

    public function setImageUpdatedAt(\DateTimeImmutable $imageUpdatedAt): void
    {
        $this->imageUpdatedAt = $imageUpdatedAt;
    }

    /**
     * Gets the public URL for this resource with cache busting.
     *
     * @param string $cdnBaseUrl The CDN base URL
     * @param string $postId The post ID
     * @return string The full URL with cache-busting version
     */
    public function getImageUrl(string $cdnBaseUrl, string $postId): string
    {
        $type = self::getResourceTypeFromId($this->resourceType);
        $path = "jee/posts/$postId/$type/" . $this->filename;
        $url = rtrim($cdnBaseUrl, '/') . '/' . $path;

        if ($this->imageUpdatedAt !== null) {
            $url .= '?v=' . $this->imageUpdatedAt->getTimestamp();
        }

        return $url;
    }
}
