<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'hashtag')]
#[ORM\Index(columns: ['tag'], name: 'idx_hashtag_tag')]
class Hashtag
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $tag;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'hashtags')]
    private Collection $posts;

    #[ORM\Embedded(class: CreatedAtValue::class, columnPrefix: false)]
    private CreatedAtValue $createdAt;

    #[ORM\Embedded(class: UpdatedAtValue::class, columnPrefix: false)]
    private UpdatedAtValue $updatedAt;

    private function __construct(
        Uuid $id,
        string $tag
    ) {
        $this->id = $id;
        $this->tag = self::normalize($tag);
        $this->posts = new ArrayCollection();
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
    }

    public static function create(Uuid $id, string $tag): self
    {
        return new self($id, $tag);
    }

    public static function normalize(string $tag): string
    {
        // Remove # if present
        $tag = ltrim($tag, '#');

        // Convert to lowercase
        $tag = mb_strtolower($tag, 'UTF-8');

        // Keep only alphanumeric characters
        $tag = preg_replace('/[^a-z0-9]/', '', $tag);

        return $tag;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getCreatedAt(): CreatedAtValue
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): UpdatedAtValue
    {
        return $this->updatedAt;
    }
}
